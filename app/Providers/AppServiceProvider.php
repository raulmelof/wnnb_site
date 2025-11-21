<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Força HTTPS se a aplicação não estiver rodando localmente (127.0.0.1)
        // Isso resolve o problema de CSS/JS não carregando no Ngrok
        if ($this->app->environment('production') || str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
        }
    }
}
