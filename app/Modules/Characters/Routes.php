<?php

use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

return static function ($router): void {

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

    $router->get(
        '/characters/{id}',
        [CharacterController::class, 'show']
    );

    $router->get(
        '/characters/{id}/edit',
        [CharacterController::class, 'edit']
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
