<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Dashboard\Controllers;

use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Services\Codex\Codex;

defined('ABSPATH') || exit;

/**
 * Dashboard Controller.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
final class DashboardController
{
    public function __construct(
        private ViewFactory $views,
        private Codex $codex
    ) {
    }

    public function index(): string
    {
        return $this->views->render(
            'dashboard.index',
            [
                'races' => $this->codex->races(),
            ]
        );
    }
}
