<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class TrialEnded extends Mailable
{
    public function __construct(
        private User $user
    ) {}

    public function build()
    {
        return $this->view('emails.trial-ended')
            ->subject('Votre période d\'essai Premium est terminée')
            ->with([
                'userName' => $this->user->name,
                'upgradeUrl' => config('app.frontend_url') . '/upgrade'
            ]);
    }
} 