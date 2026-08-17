<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Parties\PartiesServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

final class PartiesKingdom extends Kingdom
{
    public function key(): string
    {
        return 'parties';
    }

    public function provider(): string
    {
        return PartiesServiceProvider::class;
    }

    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/Parties/Routes.php',
        ];
    }

    public function registerNavigation(
        Navigation $navigation
    ): void {
        if ($navigation->has($this->key())) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'parties',
                'Fellowships',
                Icons::PARTY,
                'parties',
                30
            )
        );
    }
}
