<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

class ViewCharacterPage extends Page
{
    public function key(): string
    {
        return 'characters.view';
    }

    public function title(): string
    {
        return 'View Character';
    }

    public function path(): string
    {
        return '/characters/{id}';
    }

    public function handler(): array
    {
        return [
            CharacterController::class,
            'show',
        ];
    }
}
