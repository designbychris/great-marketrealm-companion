<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Routing\Router;

defined('ABSPATH') || exit;

/**
 * Route Service Provider.
 *
 * Registers the application's routes.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            Router::class,
            fn () => new Router($this->app)
        );
    }

    /**
     * Boot the provider.
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * Load all route definitions.
     */
    protected function loadRoutes(): void
    {
        $router = $this->app->make(Router::class);

        $routes = GMRC_PATH .
            'app/Modules/Characters/Routes.php';

        if (! file_exists($routes)) {
            return;
        }

        $register = require $routes;

        if (is_callable($register)) {
            $register($router);
        }
    }
}
