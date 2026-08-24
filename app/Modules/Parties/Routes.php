<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Parties\Controllers\PartyController;
use GreatMarketrealmCompanion\Modules\Parties\Controllers\FellowshipSealController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/fellowship-seal',
        [FellowshipSealController::class, 'index']
    );

    $router->post(
        '/fellowship-seal',
        [FellowshipSealController::class, 'redeem']
    );

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

    $router->get(
        '/parties/{id}/seal',
        [FellowshipSealController::class, 'manage']
    );

    $router->post(
        '/parties/{id}/seal',
        [FellowshipSealController::class, 'issue']
    );

    $router->delete(
        '/parties/{id}/seal',
        [FellowshipSealController::class, 'revoke']
    );

    $router->put(
        '/parties/{id}/members/{character}/role',
        [PartyController::class, 'updateMemberRole']
    );

    $router->put(
        '/parties/{id}/members/{character}/office',
        [PartyController::class, 'updateMemberOffice']
    );

    $router->delete(
        '/parties/{id}/members/{character}',
        [PartyController::class, 'removeMember']
    );

    $router->put(
        '/parties/{id}/standard',
        [PartyController::class, 'updateStandard']
    );

    $router->put(
        '/parties/{id}/charter',
        [PartyController::class, 'updateCharter']
    );

    $router->post(
        '/parties/{id}/treasury/deposit',
        [PartyController::class, 'depositTreasury']
    );

    $router->post(
        '/parties/{id}/treasury/withdraw',
        [PartyController::class, 'withdrawTreasury']
    );

    $router->post(
        '/parties/{id}/treasury/transfer',
        [PartyController::class, 'transferCoin']
    );

    $router->post(
        '/parties/{id}/chronicle/notes',
        [PartyController::class, 'addChronicleNote']
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
