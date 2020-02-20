<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Two\ClaveUnicaProvider;
use Claveunica;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use App\Search\ElasticsearchRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
    */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->bootClaveUnicaSocialite();
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts($app['config']->get('services.search.hosts'))
                ->build();
        });

        $this->app->bind(ElasticsearchRepository::class, function($app) {
            return new ElasticsearchRepository($app->make(Client::class));
        });
    }

    public function bootClaveUnicaSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('claveunica',function ($app) use ($socialite) {
            $redirect = env('APP_ENV') == 'local' ? env('CLAVEUNICA_REDIRECT') : secure_url(env('APP_URL') . env('CLAVEUNICA_REDIRECT'));
            $config = [
                'client_id' => env('CLAVEUNICA_CLIENT_ID') ,
                'client_secret' => env('CLAVEUNICA_SECRET_ID'),
                'redirect' => $redirect
            ];
            return $socialite->buildProvider(ClaveUnicaProvider::class, $config);
        });
    }
}
