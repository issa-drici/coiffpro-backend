<?php

namespace App\Providers;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use App\Infrastructure\Repositories\EloquentFileRepository;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\BarberRepositoryInterface;
use App\Infrastructure\Repositories\ClientRepository;
use App\Infrastructure\Repositories\ServiceRepository;
use App\Infrastructure\Repositories\QueueClientRepository;
use App\Infrastructure\Repositories\SalonRepository;
use App\Infrastructure\Repositories\BarberRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalonRepositoryInterface::class, SalonRepository::class);
        $this->app->bind(FileRepositoryInterface::class, EloquentFileRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(QueueClientRepositoryInterface::class, QueueClientRepository::class);
        $this->app->bind(BarberRepositoryInterface::class, BarberRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
