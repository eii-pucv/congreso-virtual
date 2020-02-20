<?php

namespace App\Providers;

use Laravel\Socialite\SocialiteServiceProvider;

class ClaveUnicaServiceProvider extends SocialiteServiceProvider
{
    public function register()
    {
        $this->app->bind('Laravel\Socialite\Contracts\Factory', function ($app) {
            return new ClaveUnicaManager($app);
        });
    }
}