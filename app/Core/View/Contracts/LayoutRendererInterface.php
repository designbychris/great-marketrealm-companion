<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\View\Contracts;

defined('ABSPATH') || exit;

/**
 * Application layout renderer contract.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
interface LayoutRendererInterface
{
    /**
     * Render the application layout.
     *
     * @param array<string,mixed> $data
     */
    public function render(
        array $data
    ): string;
}
