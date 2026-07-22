<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Base Kingdom.
 *
 * Represents a modular area of the application.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
abstract class Kingdom
{
    /**
     * Create the Kingdom.
     */
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * Unique Kingdom key.
     */
    abstract public function key(): string;

    /**
     * Kingdom service provider.
     *
     * @return class-string
     */
    abstract public function provider(): string;

    /**
     * Route files contributed by the Kingdom.
     *
     * @return array<int, string>
     */
    public function routes(): array
    {
        return [];
    }

    /**
     * Resource classes contributed by the Kingdom.
     *
     * @return array<int, class-string<\GreatMarketrealmCompanion\Resources\Resource>>
     */
    public function resources(): array
    {
        return [];
    }

    /**
     * Register navigation items contributed by the Kingdom.
     */
    public function registerNavigation(
        Navigation $navigation
    ): void {
    }

    /**
     * Access the application instance.
     */
    protected function app(): Application
    {
        return $this->app;
    }
}
