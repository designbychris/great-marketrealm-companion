<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Resources;

use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Resources\Resource;
use GreatMarketrealmCompanion\Modules\Characters\Pages\CreateCharacterPage;
use GreatMarketrealmCompanion\Modules\Characters\Pages\EditCharacterPage;
use GreatMarketrealmCompanion\Modules\Characters\Pages\ListCharactersPage;
use GreatMarketrealmCompanion\Modules\Characters\Pages\ViewCharacterPage;

defined('ABSPATH') || exit;

/**
 * Character Resource.
 *
 * Represents characters managed by the Characters Kingdom.
 *
 * @package MarketrealmCompanion
 * @since 0.4.0
 */
class CharacterResource extends Resource
{
    /**
     * Get the unique Resource key.
     */
    public function key(): string
    {
        return 'characters';
    }

    /**
     * Get the singular display name.
     */
    public function singularName(): string
    {
        return 'Character';
    }

    /**
     * Get the plural display name.
     */
    public function pluralName(): string
    {
        return 'Characters';
    }

    /**
     * Get the base route prefix.
     */
    public function routePrefix(): string
    {
        return '/characters';
    }

    /**
     * Get the Resource controller.
     *
     * @return class-string
     */
    public function controller(): string
    {
        return CharacterController::class;
    }

    public function pages(): array
    {
        return [
            ListCharactersPage::class,
            CreateCharacterPage::class,
            ViewCharacterPage::class,
            EditCharacterPage::class,
        ];
    }
}
