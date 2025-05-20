<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class DiscordHandler extends AbstractProcessingHandler
{
    private string $webhookUrl;

    public function __construct(string $webhookUrl, $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->webhookUrl = $webhookUrl;
    }

    protected function write(LogRecord $record): void
    {
        // N'envoyer que si on est en production
        if (!App::environment('production')) {
            return;
        }

        $request = request();
        $user = $request->user();
        $userInfo = $user ? "{$user->id} ({$user->email})" : 'Non authentifié';
        
        $exception = $record->context['exception'] ?? null;
        
        $errorDetails = [
            'Date' => '📅 ' . Carbon::now()->setTimezone('Europe/Paris')->format('d/m/Y H:i:s'),
            'Type' => '🔴 ' . ($exception ? get_class($exception) : 'Error'),
            'Message' => '📝 ' . $record->message,
            'Route' => '🛣️ ' . ($request->route() ? $request->route()->getName() ?? 'Sans nom' : 'Non trouvée'),
            'URL' => '🌐 ' . $request->fullUrl(),
            'Method' => '📡 ' . $request->method(),
            'File' => '📂 ' . ($exception ? $exception->getFile() : 'Unknown'),
            'Line' => '📍 ' . ($exception ? $exception->getLine() : 'Unknown'),
            'User' => '👤 ' . $userInfo,
        ];

        $message = "# 🚨 Exception détectée\n\n";
        
        foreach ($errorDetails as $key => $value) {
            if ($key === 'Trace') {
                $message .= "\n### Stack Trace\n```php\n{$value}\n```";
            } else {
                $message .= "**{$key}:** {$value}\n";
            }
        }

        Http::post($this->webhookUrl, [
            'content' => $message
        ]);
    }
}