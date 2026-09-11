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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Guard: jangan pernah jalankan test/migrate:fresh di database non-testing (mis. `tpb`).
        if ($this->app->environment('testing')) {
            $database = (string) config('database.connections.' . config('database.default') . '.database');

            if (!str_contains(strtolower($database), 'test')) {
                throw new \RuntimeException(
                    'Refusing to run testing against non-testing database "' . $database . '". '
                    . 'Set DB_DATABASE di .env.testing ke database khusus testing (nama harus memuat "test").'
                );
            }
        }
    }
}
