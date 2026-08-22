<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\DungeonMasterController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/dungeon-master',
        [DungeonMasterController::class, 'index']
    );
};
