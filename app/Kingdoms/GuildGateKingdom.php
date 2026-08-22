<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\GuildGate\GuildGateServiceProvider;
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
        return [
            GMRC_PATH . 'app/Modules/GuildGate/Routes.php',
        ];
    }

    public function registerNavigation(Navigation $navigation): void
    {
        // The Gate is deliberately absent from signed-in Guild navigation.
    }
}
