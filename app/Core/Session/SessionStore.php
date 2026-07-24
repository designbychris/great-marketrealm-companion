<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

/**
 * Session Store.
 *
 * Provides a simple interface to the underlying session storage.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class SessionStore
{
    /**
     * Determine whether a session value exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists(
            $key,
            $_SESSION
        );
    }

    /**
     * Retrieve a session value.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Store a session value.
     */
    public function put(
        string $key,
        mixed $value
    ): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Remove a session value.
     */
    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
