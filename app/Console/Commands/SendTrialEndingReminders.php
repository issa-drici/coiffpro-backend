<?php

namespace App\Console\Commands;

use App\Infrastructure\Models\EmailNotificationModel;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmailNotification;
use Carbon\Carbon;
use App\Mail\TrialEndingReminder;
use Illuminate\Support\Facades\Mail;

class SendTrialEndingReminders extends Command
{
    protected $signature = 'subscriptions:send-trial-reminders';
    protected $description = 'Send reminders to users whose trial is ending soon';

    public function handle()
    {
        // Trouver les utilisateurs dont l'essai se termine dans 7 jours
        $users = User::where('trial_ends_at', '>', Carbon::now())
            ->where('trial_ends_at', '<', Carbon::now()->addDays(7))
            ->where('user_subscription_status', 'trialing')
            ->whereDoesntHave('emailNotifications', function ($query) {
                $query->where('type', 'trial_reminder_7_days');
            })
            ->get();

        foreach ($users as $user) {
            // Calculer les jours restants
            $daysLeft = Carbon::now()->diffInDays($user->trial_ends_at);
            
            Mail::to($user->email)->send(new TrialEndingReminder($user, $daysLeft));
            
            // Enregistrer l'envoi
            EmailNotificationModel::create([
                'user_id' => $user->id,
                'type' => 'trial_reminder_7_days',
                'sent_at' => Carbon::now()
            ]);
        }
    }
} 