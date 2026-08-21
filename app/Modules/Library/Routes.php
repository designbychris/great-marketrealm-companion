<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Library\Controllers\LibraryController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/library',
        [LibraryController::class, 'index']
    );
};
