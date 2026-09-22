<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Keep redirects, forms, and Vite URLs on APP_URL, including port 8001.
        if ($rootUrl = config('app.url')) {
            URL::forceRootUrl($rootUrl);
        }

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
