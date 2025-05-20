<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Level;
use App\Logging\DiscordHandler;

class DiscordLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('discord');
        
        $webhookUrl = $config['webhook_url'];
        
        $logger->pushHandler(new DiscordHandler(
            $webhookUrl,
            Level::Error
        ));
        
        return $logger;
    }
} 