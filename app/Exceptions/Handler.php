<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Services\Discord\DiscordNotification;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e)) {
                $request = request();
                
                $errorDetails = [
                    'Type' => '🔴 ' . get_class($e),
                    'Message' => '📝 ' . $e->getMessage(),
                    'Route' => '🛣️ ' . $request->route() ? $request->route()->getName() ?? 'Sans nom' : 'Non trouvée',
                    'URL' => '🌐 ' . $request->fullUrl(),
                    'Method' => '📡 ' . $request->method(),
                    'File' => '📂 ' . str_replace(base_path(), '', $e->getFile()),
                    'Line' => '📍 ' . $e->getLine(),
                    'User' => '👤 ' . ($request->user() ? $request->user()->email : 'Non authentifié'),
                    'Trace' => $e->getTraceAsString()
                ];

                $message = "# 🚨 Exception détectée\n\n";
                
                foreach ($errorDetails as $key => $value) {
                    if ($key === 'Trace') {
                        $message .= "\n### Stack Trace\n```php\n{$value}\n```";
                    } else {
                        $message .= "**{$key}:** {$value}\n";
                    }
                }

                logger()->channel('discord')->error($message);
            }
        });
    }
} 