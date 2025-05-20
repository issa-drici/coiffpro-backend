<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class TrialEndingReminderUrgent extends Mailable
{
    public function __construct(
        private User $user
    ) {}

    public function build()
    {
        return $this->view('emails.trial-ending')
            ->subject('⚠️ Dernier rappel : Votre période d\'essai Premium se termine dans 3 jours')
            ->with([
                'userName' => $this->user->name,
                'daysLeft' => 3,
                'upgradeUrl' => config('app.frontend_url') . '/upgrade'
            ]);
    }
} 