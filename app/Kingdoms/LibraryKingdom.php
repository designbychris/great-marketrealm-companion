<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Library\LibraryServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

final class LibraryKingdom extends Kingdom
{
    public function key(): string
    {
        return 'library';
    }

    public function provider(): string
    {
        return LibraryServiceProvider::class;
    }

    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/Library/Routes.php',
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
                'library',
                'Guild Library',
                Icons::BOOK,
                'library',
                40
            )
        );
    }
}
