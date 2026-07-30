<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Exceptions\ExceptionHandler;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;

defined('ABSPATH') || exit;

/**
 * HTTP Service Provider.
 *
 * Registers the services representing the current request,
 * response creation, and application exception handling.
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
        $this->app->singleton(
            Request::class,
            static fn (): Request => Request::capture()
        );

        $this->app->singleton(
            ResponseFactory::class
        );

        $this->app->singleton(
            ExceptionHandler::class
        );
    }
}
