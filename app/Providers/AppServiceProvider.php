<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepository;
use App\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Property\PropertyRepository;
use App\Repositories\Provider\ProviderRepository;
use App\Repositories\Insurance\InsuranceRepository;
use App\Interfaces\Property\PropertyRepositoryInterface;
use App\Interfaces\Provider\ProviderRepositoryInterface;
use App\Interfaces\Insurance\InsuranceRepositoryInterface;
use App\Repositories\IncidentAction\IncidentActionRepository;
use App\Interfaces\IncidentAction\IncidentActionRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProviderRepositoryInterface::class, ProviderRepository::class);
        $this->app->bind(PropertyRepositoryInterface::class, PropertyRepository::class);
        $this->app->bind(InsuranceRepositoryInterface::class, InsuranceRepository::class);
        $this->app->bind(IncidentActionRepositoryInterface::class, IncidentActionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
