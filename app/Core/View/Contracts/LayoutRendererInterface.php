<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\View\Contracts;

defined('ABSPATH') || exit;

/**
 * Renders the application layout.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
interface LayoutRendererInterface
{
    /**
     * Render the layout.
     *
     * @param array<string,mixed> $data
     */
    public function render(
        array $data
    ): string;
}
