<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;

defined('ABSPATH') || exit;

/**
 * Character Portrait Repository Contract.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
interface CharacterPortraitRepositoryInterface
{
    /**
     * Find the portrait belonging to a Character.
     */
    public function find(
        CharacterId $characterId
    ): ?CharacterPortrait;

    /**
     * Persist a Character portrait.
     */
    public function save(
        CharacterId $characterId,
        CharacterPortrait $portrait
    ): void;

    /**
     * Remove stored portrait information.
     */
    public function delete(
        CharacterId $characterId
    ): void;
}
