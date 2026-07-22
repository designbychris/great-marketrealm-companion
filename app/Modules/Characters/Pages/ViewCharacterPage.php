<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;

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

    public function route(): string
    {
        return '/characters/{id}';
    }
}
