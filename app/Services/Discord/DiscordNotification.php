<?php

namespace App\Services\Discord;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

class DiscordNotification
{
    public static function send(string $channel, string $message): void
    {
        // N'envoyer que si on est en production
        if (App::environment('production')) {
            $webhook = Config::get("discord.webhooks.{$channel}");
            
            if (!$webhook) {
                throw new \Exception("Canal Discord non configuré : {$channel}");
            }

            Http::post($webhook, [
                'content' => $message
            ]);
        }
    }
} 