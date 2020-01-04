<?php

namespace DaveJamesMiller\RouteBrowser;

use Illuminate\Support\ServiceProvider;

/**
 * The Laravel service provider, which registers, configures and bootstraps the package.
 */
class RouteBrowserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/route-browser.php', 'route-browser');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/route-browser.php' => config_path('route-browser.php'),
        ], 'route-browser-config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'route-browser');

        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}
