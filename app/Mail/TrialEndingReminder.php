<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class TrialEndingReminder extends Mailable
{
    public function __construct(
        private User $user,
        private int $daysLeft
    ) {}

    public function build()
    {
        return $this->view('emails.trial-ending')
            ->with([
                'userName' => $this->user->name,
                'daysLeft' => $this->daysLeft,
                'upgradeUrl' => config('app.frontend_url') . '/upgrade'
            ]);
    }
} 