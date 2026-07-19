<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Character Repository.
 *
 * Handles persistence for Character domain models.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class CharacterRepository
{
    /**
     * Return all characters.
     *
     * @return Character[]
     */
    public function all(): array
    {
        // Placeholder until custom post type/data layer is implemented.
        return [];
    }

    /**
     * Find a character by ID.
     */
    public function find(int $id): ?Character
    {
        // Placeholder implementation.
        return null;
    }

    /**
     * Create a new character.
     */
    public function create(Character $character): Character
    {
        // Persistence will be added later.
        return $character;
    }

    /**
     * Update an existing character.
     */
    public function update(Character $character): Character
    {
        // Persistence will be added later.
        return $character;
    }

    /**
     * Delete a character.
     */
    public function delete(Character $character): bool
    {
        // Persistence will be added later.
        return true;
    }
}
