<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Application\Routing\Router;
use GreatMarketrealmCompanion\Core\Container;

defined('ABSPATH') || exit;

/**
 * Route Service Provider.
 *
 * Registers the routing service.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app
            ->container()
            ->singleton(
                Router::class,
                function (Container $container): Router {
                    return new Router();
                }
            );
    }

    /**
     * Boot services.
     */
    public function boot(): void
    {
        // Reserved for future routing events.
    }

    protected function registerRoutes(): void
    {
        $path = GMRC_PATH . 'app/Modules/Characters/Routes.php';
    
        if (file_exists($path)) {
            $routes = require $path;
    
            $routes($this->app->make(Router::class));
        }
    }
}
