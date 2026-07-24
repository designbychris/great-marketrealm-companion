<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

/**
 * Session Manager.
 *
 * Manages the lifecycle of the native PHP session.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class SessionManager
{
    /**
     * Start the PHP session when possible.
     */
    public function start(): void
    {
        if ($this->started()) {
            return;
        }

        if (headers_sent()) {
            return;
        }

        session_start();
    }

    /**
     * Determine whether the PHP session is active.
     */
    public function started(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
