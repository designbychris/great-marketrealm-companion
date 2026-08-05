<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRecipeGenerator;

defined('ABSPATH') || exit;

/**
 * Create Character Action.
 *
 * Persists a new Character and its initial
 * Guild-generated portrait recipe.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CreateCharacterAction extends Action
{
    public function __construct(
        private CharacterRepositoryInterface $characters,
        private CharacterPortraitRepositoryInterface $portraits,
        private PortraitRecipeGenerator $portraitRecipes
    ) {
    }

    /**
     * Persist a new Character.
     */
    public function handle(
        Character $character
    ): Character {
        $this->characters->save(
            $character
        );

        $recipe = $this
            ->portraitRecipes
            ->forCharacter($character);

        $this->portraits->save(
            $character->id(),
            CharacterPortrait::generated(
                $recipe
            )
        );

        return $character;
    }
}
