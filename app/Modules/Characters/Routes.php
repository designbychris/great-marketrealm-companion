<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

return static function (Router $router): void {

    error_log('Characters Routes.php registrar executed');

    $router->get(
        '/characters',
        [CharacterController::class, 'index']
    );

    $router->get(
        '/characters/create',
        [CharacterController::class, 'create']
    );

    $router->post(
        '/characters',
        [CharacterController::class, 'store']
    );

    $router->put(
        '/characters/{id}',
        [CharacterController::class, 'update']
    );

    $router->delete(
        '/characters/{id}',
        [CharacterController::class, 'destroy']
    );
};
