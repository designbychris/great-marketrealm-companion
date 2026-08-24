<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Honours\Controllers\HonoursController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get('/guild-honours', [HonoursController::class, 'index']);
};
