<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Honours\HonoursServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

final class HonoursKingdom extends Kingdom
{
    public function key(): string
    {
        return 'honours';
    }

    public function provider(): string
    {
        return HonoursServiceProvider::class;
    }

    public function routes(): array
    {
        return [GMRC_PATH . 'app/Modules/Honours/Routes.php'];
    }

    public function registerNavigation(Navigation $navigation): void
    {
        if ($navigation->has($this->key())) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'honours',
                'Guild Honours',
                Icons::HONOURS,
                'guild-honours',
                45
            )
        );
    }
}
