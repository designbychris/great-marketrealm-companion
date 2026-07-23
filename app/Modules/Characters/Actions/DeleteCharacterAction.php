<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;

defined('ABSPATH') || exit;

/**
 * Delete Character Action.
 *
 * Deletes an existing Character.
 *
 * @package MarketrealmCompanion
 * @since 0.5.0
 */
class DeleteCharacterAction extends Action
{
    public function __construct(
        protected CharacterRepository $characters
    ) {
    }

    /**
     * Delete a Character by its identifier.
     */
    public function handle(
        int $id
    ): bool {
        return $this->characters->delete(
            $id
        );
    }
}
