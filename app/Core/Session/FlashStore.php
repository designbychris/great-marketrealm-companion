<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

/**
 * Flash Store.
 *
 * Stores temporary data for the next request.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class FlashStore
{
    /**
     * Session key.
     */
    protected const KEY = 'gmrc_flash';

    /**
     * Store flash data.
     */
    public function put(
        string $key,
        mixed $value
    ): void {
        $_SESSION[self::KEY][$key] = $value;
    }

    /**
     * Determine whether a key exists.
     */
    public function has(
        string $key
    ): bool {
        return isset(
            $_SESSION[self::KEY][$key]
        );
    }

    /**
     * Retrieve flash data.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        if (! $this->has($key)) {
            return $default;
        }

        $value = $_SESSION[self::KEY][$key];

        unset(
            $_SESSION[self::KEY][$key]
        );

        return $value;
    }

    /**
     * Remove a key.
     */
    public function forget(
        string $key
    ): void {
        unset(
            $_SESSION[self::KEY][$key]
        );
    }

    /**
     * Remove all flash data.
     */
    public function clear(): void
    {
        unset(
            $_SESSION[self::KEY]
        );
    }
}
