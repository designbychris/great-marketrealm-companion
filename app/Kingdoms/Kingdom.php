<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

/**
 * Base Kingdom.
 *
 * Represents a self-contained Marketrealm Companion module.
 *
 * Each Kingdom may provide:
 *
 * - A service provider
 * - Route files
 * - Navigation items
 * - Future Kingdom-specific configuration
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
abstract class Kingdom
{
    /**
     * Application instance.
     */
    protected Application $app;

    /**
     * Constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Return the Kingdom's unique key.
     *
     * Examples:
     *
     * dashboard
     * characters
     * campaigns
     */
    abstract public function key(): string;

    /**
     * Return the Kingdom's service provider class.
     *
     * @return class-string<ServiceProvider>
     */
    abstract public function provider(): string;

    /**
     * Return the Kingdom's route files.
     *
     * A Kingdom may return multiple route files later, such as:
     *
     * - Routes.php
     * - ApiRoutes.php
     * - AdminRoutes.php
     *
     * @return array<int, string>
     */
    public function routes(): array
    {
        return [];
    }

    /**
     * Register the Kingdom's navigation items.
     *
     * Kingdoms without navigation may leave this method untouched.
     */
    public function registerNavigation(
        Navigation $navigation
    ): void {
        // No navigation items by default.
    }

    /**
     * Return the application instance.
     */
    protected function app(): Application
    {
        return $this->app;
    }
}
