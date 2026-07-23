<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\ServiceProvider;

defined('ABSPATH') || exit;

/**
 * HTTP Service Provider.
 *
 * Registers HTTP-related services.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.6.0
 */
class HttpServiceProvider extends ServiceProvider
{
    /**
     * Register HTTP services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            Request::class,
            static fn (): Request =>
                Request::capture()
        );

        $this->app->container()->singleton(
            ResponseFactory::class,
            static fn (): ResponseFactory =>
                new ResponseFactory()
        );
    }
}
