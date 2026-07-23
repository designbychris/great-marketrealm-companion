<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;

defined('ABSPATH') || exit;

/**
 * Create Character Action.
 *
 * Persists a new Character.
 *
 * @package MarketrealmCompanion
 * @since 0.5.0
 */
class CreateCharacterAction extends Action
{
    public function __construct(
        protected CharacterRepository $characters
    ) {
    }

    /**
     * Create a Character.
     */
    public function handle(
        Character $character
    ): Character {
        return $this->characters->create(
            $character
        );
    }
}
