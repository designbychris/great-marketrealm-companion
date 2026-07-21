<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Routing\Router;

defined('ABSPATH') || exit;

/**
 * Route Service Provider.
 *
 * Registers the application's routing services.
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
            fn (): Router => new Router(
                $this->app->container()
            )
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
     * Load all application route files.
     */
    protected function loadRoutes(): void
    {
        $router = $this->app->make(Router::class);

        $routeFiles = [
            GMRC_PATH . 'app/Modules/Characters/Routes.php',
        ];

        foreach ($routeFiles as $routeFile) {
            if (! file_exists($routeFile)) {
                continue;
            }

            $routeRegistrar = require $routeFile;

            if (is_callable($routeRegistrar)) {
                $routeRegistrar($router);
            }
        }
    }
}
