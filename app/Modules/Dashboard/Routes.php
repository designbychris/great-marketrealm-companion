<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Dashboard\Controllers\DashboardController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/dashboard',
        [DashboardController::class, 'index']
    );
};
