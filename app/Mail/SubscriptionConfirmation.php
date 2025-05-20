<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class SubscriptionConfirmation extends Mailable
{
    public function __construct(
        private User $user
    ) {}

    public function build()
    {
        return $this->view('emails.subscription-confirmation')
            ->subject('Bienvenue dans le plan Premium ! 🎉')
            ->with([
                'userName' => $this->user->name,
                'dashboardUrl' => config('app.frontend_url') . '/admin/dashboard'
            ]);
    }
}
