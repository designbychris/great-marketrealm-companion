<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

/**
 * Character Repository Contract.
 *
 * Defines persistence operations for Character entities.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
interface CharacterRepositoryInterface
{
    /**
     * Retrieve all Characters.
     *
     * @return Character[]
     */
    public function all(): array;

    /**
     * Find a Character by its identifier.
     */
    public function find(
        CharacterId $id
    ): ?Character;

    /**
     * Persist a Character.
     */
    public function save(
        Character $character
    ): void;

    /**
     * Delete a Character.
     */
    public function delete(
        CharacterId $id
    ): void;
}
