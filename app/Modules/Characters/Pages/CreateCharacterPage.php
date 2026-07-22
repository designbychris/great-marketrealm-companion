<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;

defined('ABSPATH') || exit;

class CreateCharacterPage extends Page
{
    public function key(): string
    {
        return 'characters.create';
    }

    public function title(): string
    {
        return 'Create Character';
    }

    public function route(): string
    {
        return '/characters/create';
    }
}
