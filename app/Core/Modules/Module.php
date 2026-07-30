<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Modules;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

/**
 * Base application module.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
abstract class Module
{
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * Return the unique module identifier.
     */
    abstract public function key(): string;

    /**
     * Return service providers contributed by the module.
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    public function providers(): array
    {
        return [];
    }

    /**
     * Register module-specific services.
     */
    public function register(): void
    {
    }

    /**
     * Boot the module after all providers are registered.
     */
    public function boot(): void
    {
    }
}
