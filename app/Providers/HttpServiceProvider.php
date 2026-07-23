<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Http\Request;

defined('ABSPATH') || exit;

/**
 * HTTP Service Provider.
 *
 * Registers the application's HTTP services.
 *
 * @package MarketrealmCompanion
 * @since 0.6.0
 */
class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->container()->singleton(
            Request::class,
            static fn (): Request =>
                Request::capture()
        );
    }

    public function boot(): void
    {
        // HTTP boot logic will live here later.
    }
}
