<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\DungeonMaster\DungeonMasterServiceProvider;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Dungeon Master Kingdom.
 *
 * The navigation contribution is capability-aware, while the controller
 * independently enforces the same boundary for direct route requests.
 */
final class DungeonMasterKingdom extends Kingdom
{
    public function key(): string
    {
        return 'dungeon-master';
    }

    public function provider(): string
    {
        return DungeonMasterServiceProvider::class;
    }

    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/DungeonMaster/Routes.php',
        ];
    }

    public function registerNavigation(Navigation $navigation): void
    {
        if ($navigation->has($this->key())) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'dungeon-master',
                "Dungeon Master's Desk",
                Icons::DUNGEON_MASTER,
                'dungeon-master',
                50,
                null,
                GuildRoleRegistrar::MANAGE_CAMPAIGNS
            )
        );
    }
}
