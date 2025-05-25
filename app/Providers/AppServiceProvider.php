<?php

namespace App\Providers;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Domain\Repositories\SalonRepositoryInterface;
use App\Infrastructure\Repositories\EloquentSalonRepository;
use App\Infrastructure\Repositories\EloquentFileRepository;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Repositories\ClientRepository;
use App\Infrastructure\Repositories\ServiceRepository;
use App\Infrastructure\Repositories\QueueClientRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalonRepositoryInterface::class, EloquentSalonRepository::class);
        $this->app->bind(FileRepositoryInterface::class, EloquentFileRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(QueueClientRepositoryInterface::class, QueueClientRepository::class);
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
