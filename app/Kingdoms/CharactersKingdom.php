<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Characters\CharactersServiceProvider;
use GreatMarketrealmCompanion\Modules\Characters\Resources\CharacterResource;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

class CharactersKingdom extends Kingdom
{
    public function key(): string
    {
        return 'characters';
    }

    public function provider(): string
    {
        return CharactersServiceProvider::class;
    }

    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/Characters/Routes.php',
        ];
    }

    public function resources(): array
    {
        return [
            CharacterResource::class,
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
                'characters',
                'Characters',
                Icons::USERS,
                'characters',
                20
            )
        );
    }
}
