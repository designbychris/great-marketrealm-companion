<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

return static function (
    Router $router
): void {

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
        '/characters/{id}/edit',
        [CharacterController::class, 'edit']
    );

    $router->get(
        '/characters/{id}/delete',
        [CharacterController::class, 'confirmDelete']
    );

    $router->post(
        '/characters/{id}/portrait',
        [CharacterController::class, 'uploadPortrait']
    );

    $router->post(
        '/characters/{id}/inventory',
        [CharacterController::class, 'addInventoryItem']
    );

    $router->post(
        '/characters/{id}/vital-measures',
        [CharacterController::class, 'updateVitalMeasures']
    );

    $router->post(
        '/characters/{id}/resources/spend',
        [CharacterController::class, 'spendClassResource']
    );

    $router->post(
        '/characters/{id}/resources/refresh',
        [CharacterController::class, 'refreshClassResources']
    );

    $router->post(
        '/characters/{id}/rage/enter',
        [CharacterController::class, 'enterRage']
    );

    $router->post(
        '/characters/{id}/rage/end',
        [CharacterController::class, 'endRage']
    );

    $router->post(
        '/characters/{id}/rage/rest',
        [CharacterController::class, 'restRage']
    );

    $router->post(
        '/characters/{id}/purse/deposit',
        [CharacterController::class, 'depositPurse']
    );

    $router->post(
        '/characters/{id}/purse/withdraw',
        [CharacterController::class, 'withdrawPurse']
    );

    $router->post(
        '/characters/{id}/progression/experience',
        [CharacterController::class, 'addExperience']
    );

    $router->get(
        '/characters/{id}/progression/advance',
        [CharacterController::class, 'advancement']
    );

    $router->post(
        '/characters/{id}/progression/advance/choice',
        [CharacterController::class, 'recordAdvancementChoice']
    );

    $router->post(
        '/characters/{id}/progression/advance/certify',
        [CharacterController::class, 'certifyAdvancement']
    );


    $router->put(
        '/characters/{id}/inventory/{item}',
        [CharacterController::class, 'updateInventoryItem']
    );

    $router->post(
        '/characters/{id}/inventory/{item}/equip',
        [CharacterController::class, 'equipInventoryItem']
    );

    $router->delete(
        '/characters/{id}/inventory/{item}',
        [CharacterController::class, 'removeInventoryItem']
    );

    $router->delete(
        '/characters/{id}/portrait',
        [CharacterController::class, 'resetPortrait']
    );

    $router->get(
        '/characters/{id}',
        [CharacterController::class, 'show']
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
