<?php

namespace GreatMarketrealmCompanion\Application\Routing;

defined('ABSPATH') || exit;

/**
 * Router
 *
 * Provides information about the current request and
 * generates URLs for Marketrealm Companion pages.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Router
{
    /**
     * Return the current route.
     */
    public function current(): string
    {
        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );

        return is_string($page) && $page !== ''
            ? $page
            : 'dashboard';
    }

    /**
     * Determine whether the current route matches.
     */
    public function is(string $route): bool
    {
        return $this->current() === $route;
    }

    /**
     * Generate a URL for a route.
     */
    public function url(string $route): string
    {
        return admin_url(
            'admin.php?page=' . urlencode($route)
        );
    }
}
