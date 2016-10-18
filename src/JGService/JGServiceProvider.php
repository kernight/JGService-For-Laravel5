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
            __DIR__ . '/../config/showapi.config.php' => config_path('JGService-showapi.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('SteveLiuXu\JGService\JGService', function ($app) {
            $class = new JGService();
            $class =  $class->SetShowApiParam($app->config->get('JGService-showapi.showapi_appid'),$app->config->get('JGService-showapi.showapi_secret'),$app->config->get('JGService-showapi.showapi_url'));
            return $class;
        });
    }


}