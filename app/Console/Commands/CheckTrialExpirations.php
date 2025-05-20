<?php

namespace App\Console\Commands;

use App\Infrastructure\Models\EmailNotificationModel;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\TrialEnded;
use Illuminate\Support\Facades\Mail;

class CheckTrialExpirations extends Command
{
    protected $signature = 'subscriptions:check-trials';
    protected $description = 'Check and update expired trial subscriptions';

    public function handle()
    {
        $users = User::where('user_subscription_status', 'trialing')
            ->where('trial_ends_at', '<', Carbon::now())
            ->where('user_plan', 'premium')
            ->whereDoesntHave('emailNotifications', function ($query) {
                $query->where('type', 'trial_ended');
            })
            ->get();

        foreach ($users as $user) {
            // Mettre à jour le statut
            $user->update([
                'user_plan' => 'basic',
                'user_subscription_status' => 'expired'
            ]);

            // Envoyer le mail
            Mail::to($user->email)->send(new TrialEnded($user));
            
            // Enregistrer l'envoi
            EmailNotificationModel::create([
                'user_id' => $user->id,
                'type' => 'trial_ended',
                'sent_at' => Carbon::now()
            ]);
        }
    }
} 