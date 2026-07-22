<?php

namespace GreatMarketrealmCompanion\Modules\Dashboard\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;

defined('ABSPATH') || exit;

/**
 * Dashboard Controller.
 *
 * Handles requests for the Companion dashboard.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class DashboardController
{
    public function __construct(
        protected ViewFactory $views
    ) {
    }

    /**
     * Display the dashboard.
     */
    public function index(): string
    {
        return $this->views->render(
            View::make('dashboard.index')
        );
    }
}
