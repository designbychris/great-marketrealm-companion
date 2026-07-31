<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Http\Contracts;

defined('ABSPATH') || exit;

/**
 * Resolves the current application route.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
interface RouteResolverInterface
{
    /**
     * Return the current route.
     */
    public function current(): string;
}
