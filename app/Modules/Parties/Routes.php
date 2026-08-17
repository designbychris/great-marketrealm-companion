<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Parties\Controllers\PartyController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/parties',
        [PartyController::class, 'index']
    );

    $router->get(
        '/parties/create',
        [PartyController::class, 'create']
    );

    $router->post(
        '/parties',
        [PartyController::class, 'store']
    );

    $router->get(
        '/parties/{id}/edit',
        [PartyController::class, 'edit']
    );

    $router->post(
        '/parties/{id}/members',
        [PartyController::class, 'addMember']
    );

    $router->put(
        '/parties/{id}/members/{character}/role',
        [PartyController::class, 'updateMemberRole']
    );

    $router->delete(
        '/parties/{id}/members/{character}',
        [PartyController::class, 'removeMember']
    );

    $router->put(
        '/parties/{id}/standard',
        [PartyController::class, 'updateStandard']
    );

    $router->get(
        '/parties/{id}',
        [PartyController::class, 'show']
    );

    $router->put(
        '/parties/{id}',
        [PartyController::class, 'update']
    );

    $router->delete(
        '/parties/{id}',
        [PartyController::class, 'destroy']
    );
};
