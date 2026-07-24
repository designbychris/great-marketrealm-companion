<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

class SessionManager
{
    /**
     * Start the PHP session if required.
     */
    public function start(): void
    {
        if (
            session_status() === PHP_SESSION_NONE &&
            ! headers_sent()
        ) {
            session_start();
        }
    }

    /**
     * Determine whether a session exists.
     */
    public function started(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
