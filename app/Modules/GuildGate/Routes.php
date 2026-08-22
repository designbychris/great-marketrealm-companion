<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\GuildGate\Controllers\GuildGateController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->post(
        '/guild-gate/login',
        [GuildGateController::class, 'login']
    );

    $router->post(
        '/guild-gate/register',
        [GuildGateController::class, 'register']
    );
};
