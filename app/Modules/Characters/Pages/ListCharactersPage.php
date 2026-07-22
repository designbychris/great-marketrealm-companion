<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;

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

    public function route(): string
    {
        return '/characters';
    }
}
