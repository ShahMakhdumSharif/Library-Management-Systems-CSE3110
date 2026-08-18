<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $oracle = config('database.connections.oracle');
        $migrations = config('database.migrations');

        config()->set('database', [
            'default' => 'oracle',
            'connections' => [
                'oracle' => $oracle,
            ],
            'migrations' => $migrations,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}