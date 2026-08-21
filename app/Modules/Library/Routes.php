<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Library\Controllers\LibraryController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/library',
        [LibraryController::class, 'index']
    );

    $router->get(
        '/library/spells',
        [LibraryController::class, 'spells']
    );

    $router->get(
        '/library/backgrounds',
        [LibraryController::class, 'backgrounds']
    );
};
