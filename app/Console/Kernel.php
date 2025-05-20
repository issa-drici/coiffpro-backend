<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Vérifier les essais expirés chaque jour à minuit
        $schedule->command('subscriptions:check-trials')->dailyAt('00:00');
        
        // Envoyer les rappels 7 jours avant expiration à 8h30
        $schedule->command('subscriptions:send-trial-reminders')->dailyAt('08:30');
        
        // Envoyer les rappels 3 jours avant expiration à 8h30
        $schedule->command('subscriptions:send-trial-reminders-urgent')->dailyAt('08:30');
    }
} 