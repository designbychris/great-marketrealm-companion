<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

/**
 * Delete Character Action.
 *
 * Deletes a Character using its domain identifier.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class DeleteCharacterAction extends Action
{
    public function __construct(
        private CharacterRepositoryInterface $characters
    ) {
    }

    /**
     * Delete a Character by its ULID.
     */
    public function handle(
        CharacterId $id
    ): void {
        $this->characters->delete(
            $id
        );
    }
}
