<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;

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
        private CharacterRepositoryInterface $characters,
        private ?CharacterPortraitRepositoryInterface $portraits = null
    ) {
    }

    /**
     * Delete a Character by its ULID.
     */
    public function handle(
        CharacterId $id
    ): void {
        /*
         * Portrait metadata lives on the Character post, so it must be
         * removed before the Character repository permanently deletes
         * that post.
         */
        if (
            $this->portraits
                instanceof CharacterPortraitRepositoryInterface
        ) {
            $this->portraits->delete($id);
        }

        $this->characters->delete(
            $id
        );
    }
}
