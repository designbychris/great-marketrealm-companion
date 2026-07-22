<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;

defined('ABSPATH') || exit;

class EditCharacterPage extends Page
{
    public function key(): string
    {
        return 'characters.edit';
    }

    public function title(): string
    {
        return 'Edit Character';
    }

    public function route(): string
    {
        return '/characters/{id}/edit';
    }
}
