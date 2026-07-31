<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Http;

use GreatMarketrealmCompanion\Core\Http\Contracts\RouteResolverInterface;

defined('ABSPATH') || exit;

/**
 * Resolves the current Companion route.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
final class RouteResolver implements RouteResolverInterface
{
    /**
     * Resolve the requested route.
     */
    public function current(): string
    {
        if (isset($_POST['gmrc_route'])) {
            $route = sanitize_text_field(
                wp_unslash($_POST['gmrc_route'])
            );
        } elseif (isset($_GET['gmrc_route'])) {
            $route = sanitize_text_field(
                wp_unslash($_GET['gmrc_route'])
            );
        } else {
            $route = 'dashboard';
        }

        $route = preg_replace(
            '#[^a-zA-Z0-9/_-]#',
            '',
            $route
        );

        $route = trim(
            is_string($route)
                ? $route
                : '',
            '/'
        );

        return $route !== ''
            ? $route
            : 'dashboard';
    }
}
