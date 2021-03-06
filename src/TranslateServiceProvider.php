<?php

namespace Weglot\Translate;

use Illuminate\Support\ServiceProvider;
use Weglot\Translate\Commands\CacheClearCommand;
use Weglot\Translate\Providers\BladeServiceProvider;
use Weglot\Translate\Providers\RouterServiceProvider;
use Weglot\Translate\Routing\UrlGenerator;

/**
 * Class TranslateServiceProvider
 * @package Weglot\Translate
 */
class TranslateServiceProvider extends ServiceProvider
{
    /**
     * Library version
     *
     * @var string
     */
    const VERSION = '0.3.6';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // views
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/weglot-translate')
        ], 'views');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'weglot-translate');

        // publish & use custom configuration
        $this->publishes([
            __DIR__ . '/../resources/config/config.php' => config_path('weglot-translate.php')
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/config.php',
            'weglot-translate'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                CacheClearCommand::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(BladeServiceProvider::class);
        $this->app->register(RouterServiceProvider::class);

        $routes = $this->app['router']->getRoutes();
        $this->app->instance('url', new UrlGenerator($routes, $this->app->make('request')));

        $this->app->bind('weglot.cache', 'Weglot\\Translate\\Cache\\Cache');
    }
}
