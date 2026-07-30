<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\Session\SessionManager;
use GreatMarketrealmCompanion\Core\Session\SessionStore;

defined('ABSPATH') || exit;

/**
 * Session Service Provider.
 *
 * Registers and boots the framework session services.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.7.0
 */
class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register session services.
     */
    public function register(): void
    {
        $this->app->singleton(
            SessionManager::class
        );

        $this->app->singleton(
            SessionStore::class
        );

        $this->app->singleton(
            FlashStore::class
        );
    }

    /**
     * Start the session and age its flash data.
     */
    public function boot(): void
    {
        $session = $this->app->make(
            SessionManager::class
        );

        $session->start();

        if (! $session->started()) {
            return;
        }

        $this->app->make(
            FlashStore::class
        )->age();
    }
}
