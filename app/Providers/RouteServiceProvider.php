<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Providers\ServiceProvider;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Route Service Provider.
 *
 * Registers the Router and loads routes contributed
 * by installed Kingdoms.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register routing services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            Router::class,
            fn (): Router => new Router(
                $this->app->container(),
                $this->app->make(
                    Request::class
                )
            )
        );
    }

    /**
     * Boot application routes.
     */
    public function boot(): void
    {
        $router = $this->app->make(
            Router::class
        );

        $registry = $this->app->make(
            KingdomRegistry::class
        );

        foreach ($registry->routeFiles() as $routeFile) {
            $this->loadRouteFile(
                $routeFile,
                $router
            );
        }

        do_action(
            'gmrc_kingdom_routes_registered',
            $router,
            $registry
        );
    }

    /**
     * Load a Kingdom route file.
     */
    protected function loadRouteFile(
        string $routeFile,
        Router $router
    ): void {
        if (! is_file($routeFile)) {
            throw new RuntimeException(
                sprintf(
                    'Kingdom route file not found: %s',
                    $routeFile
                )
            );
        }

        $routeRegistrar = require $routeFile;

        if (! is_callable($routeRegistrar)) {
            throw new RuntimeException(
                sprintf(
                    'Kingdom route file must return a callable: %s',
                    $routeFile
                )
            );
        }

        $routeRegistrar($router);
    }
}
