<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;
use Carbon\Carbon;

class WelcomeTrialStarted extends Mailable
{
    public function __construct(
        private User $user
    ) {}

    public function build()
    {
        return $this->view('emails.welcome-trial')
            ->subject('Bienvenue sur EatsUp !')
            ->with([
                'userName' => $this->user->name,
                'trialStartDate' => Carbon::now()->format('d/m/Y'),
                'trialEndDate' => Carbon::parse($this->user->trial_ends_at)->format('d/m/Y'),
                'upgradeUrl' => config('app.frontend_url') . '/admin/subscription'
            ]);
    }
} 