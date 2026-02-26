<?php

namespace App\Providers;

use App\Models\Savings;
use App\Observers\PaymentObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register failover manager as singleton
        $this->app->singleton(\App\Services\DatabaseFailoverManager::class);
        $this->app->singleton(\App\Services\WriteQueueService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Savings::observe(PaymentObserver::class);

        // Set up database failover
        if (config('database.failover_enabled', true)) {
            $failoverManager = app(\App\Services\DatabaseFailoverManager::class);
            $activeConnection = $failoverManager->getActiveConnection();
            config(['database.default' => $activeConnection]);

            // Share failover status with all views
            view()->share('isReadOnlyMode', $failoverManager->isUsingLocalDatabase());
            view()->share('pendingOperations', app(\App\Services\WriteQueueService::class)->getPendingCount());
        }
    }
}
