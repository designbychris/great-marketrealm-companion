<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\GuildGate\Controllers\GuildGateController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->post('/guild-gate/login', [GuildGateController::class, 'login']);
    $router->post('/guild-gate/register', [GuildGateController::class, 'register']);

    $router->get('/guild-profile', [GuildGateController::class, 'profile']);
    $router->post('/guild-profile', [GuildGateController::class, 'updateProfile']);
    $router->post('/guild-profile/portrait', [GuildGateController::class, 'uploadPortrait']);
    $router->delete('/guild-profile/portrait', [GuildGateController::class, 'removePortrait']);
};
