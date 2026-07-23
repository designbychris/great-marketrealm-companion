<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

class ListCharactersPage extends Page
{
    public function key(): string
    {
        return 'characters.index';
    }

    public function title(): string
    {
        return 'Characters';
    }

    public function path(): string
    {
        return '/characters';
    }

    public function handler(): array
    {
        return [
            CharacterController::class,
            'index',
        ];
    }
}
