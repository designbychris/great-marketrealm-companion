<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

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

    public function path(): string
    {
        return '/characters/{id}/edit';
    }

    public function handler(): array
    {
        return [
            CharacterController::class,
            'edit',
        ];
    }
}
