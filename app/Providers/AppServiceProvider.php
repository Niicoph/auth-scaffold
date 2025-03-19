<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

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
    public function boot()
    {
        Socialite::extend('google', function ($app) {
            return Socialite::buildProvider(\Laravel\Socialite\Two\GoogleProvider::class, [
                'client_id'     => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect'      => env('GOOGLE_REDIRECT_URI'),
            ])->stateless();
        });
    }
}
