<?php

namespace App\Console\Commands;

use App\Infrastructure\Models\EmailNotificationModel;
use App\Models\EmailNotification;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\TrialEndingReminderUrgent;
use Illuminate\Support\Facades\Mail;

class SendTrialEndingRemindersUrgent extends Command
{
    protected $signature = 'subscriptions:send-trial-reminders-urgent';
    protected $description = 'Send urgent reminders to users whose trial is ending in 3 days';

    public function handle()
    {
        $users = User::where('trial_ends_at', '>', Carbon::now())
            ->where('trial_ends_at', '<', Carbon::now()->addDays(3))
            ->where('user_subscription_status', 'trialing')
            ->whereDoesntHave('emailNotifications', function ($query) {
                $query->where('type', 'trial_reminder_3_days');
            })
            ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new TrialEndingReminderUrgent($user));
            
            EmailNotificationModel::create([
                'user_id' => $user->id,
                'type' => 'trial_reminder_3_days',
                'sent_at' => Carbon::now()
            ]);
        }
    }
} 