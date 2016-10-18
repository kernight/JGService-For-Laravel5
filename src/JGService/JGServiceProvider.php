<?php
namespace steveLiuxu\JGService;


use Illuminate\Support\ServiceProvider;


class JGServiceProvider extends ServiceProvider{

    /**
     * boot process
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/showapi.config.php' => config_path('steveLiuXU-JGService-showapi.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/showapi.config.php', 'JDService-showapi');

        $this->app->singleton('steveLiuXu\Contracts\Connection', function ($app) {
            return new Connection(config('riak'));
        });
    }

}