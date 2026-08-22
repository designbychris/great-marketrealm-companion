<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\GuildGate\GuildGateServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

final class GuildGateKingdom extends Kingdom
{
    public function key(): string
    {
        return 'guild-gate';
    }

    public function provider(): string
    {
        return GuildGateServiceProvider::class;
    }

    public function routes(): array
    {
        return [GMRC_PATH . 'app/Modules/GuildGate/Routes.php'];
    }

    public function registerNavigation(Navigation $navigation): void
    {
        if ($navigation->has('guild-profile')) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'guild-profile',
                'Guild Profile',
                Icons::SETTINGS,
                'guild-profile',
                90
            )
        );
    }
}
