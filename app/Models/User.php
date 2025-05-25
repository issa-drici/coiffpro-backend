<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Infrastructure\Models\EmailNotificationModel;
use App\Infrastructure\Models\SalonModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'role',
        'user_plan',
        'user_subscription_status',
        'trial_ends_at',
        'email_verified_at',
        'salon_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function salon()
    {
        return $this->hasOne(SalonModel::class, 'owner_id');
    }

    /**
     * Get the Stripe customer ID column name.
     */
    public function getStripeIdColumn(): string
    {
        return 'stripe_user_customer_id';
    }

    /**
     * Get the Stripe subscription ID column name.
     */
    public function getSubscriptionStripeIdColumn(): string
    {
        return 'stripe_user_subscription_id';
    }

    public function emailNotifications()
    {
        return $this->hasMany(EmailNotificationModel::class);
    }
}
