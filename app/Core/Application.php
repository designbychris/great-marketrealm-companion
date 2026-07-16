<?php

namespace GreatMarketrealmCompanion\Core;

defined('ABSPATH') || exit;

/**
 * Class Application
 *
 * Entry point for the Great Marketrealm Companion Platform.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Application
{
    /**
     * Framework kernel.
     */
    protected Kernel $kernel;

    /**
     * Create the application.
     */
    public function __construct()
    {
        $this->kernel = new Kernel($this);
    }

    /**
     * Boot the application.
     */
    public function boot(): void
    {
        $this->kernel->boot();
    }

    /**
     * Get the kernel.
     */
    public function kernel(): Kernel
    {
        return $this->kernel;
    }
}
