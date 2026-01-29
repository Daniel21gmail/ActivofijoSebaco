<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
        // Detect ngrok/proxy and force HTTPS + Root URL
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');

            // Si viene de un proxy, usamos el Host que nos pasan para que los assets carguen bien
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
            }
        }

        Vite::prefetch(concurrency: 3);
    }
}
