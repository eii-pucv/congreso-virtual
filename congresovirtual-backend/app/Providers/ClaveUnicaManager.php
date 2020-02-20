<?php

namespace App\Providers;

use App\Auth\SocialiteClaveUnicaDriver;
use Laravel\Socialite\SocialiteManager;

class ClaveUnicaManager extends SocialiteManager
{
    protected function createClaveUnicaDriver()
    {
        $config = $this->app['config']['services.ClaveUnica'];

        return $this->buildProvider(
            SocialiteClaveUnicaDriver::class, $config
        );
    }
}