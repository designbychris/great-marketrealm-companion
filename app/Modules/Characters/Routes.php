<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

return static function (Router $router): void {

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
