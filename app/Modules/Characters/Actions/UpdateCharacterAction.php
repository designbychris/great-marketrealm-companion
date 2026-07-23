<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;

defined('ABSPATH') || exit;

/**
 * Update Character Action.
 *
 * Persists changes to an existing Character.
 *
 * @package MarketrealmCompanion
 * @since 0.5.0
 */
class UpdateCharacterAction extends Action
{
    public function __construct(
        protected CharacterRepository $characters
    ) {
    }

    /**
     * Update a Character.
     */
    public function handle(
        Character $character
    ): Character {
        return $this->characters->update(
            $character
        );
    }
}
