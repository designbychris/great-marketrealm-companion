<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Create Character Action.
 *
 * Persists a newly created Character entity.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CreateCharacterAction extends Action
{
    public function __construct(
        private CharacterRepositoryInterface $characters
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

        return $character;
    }
}
