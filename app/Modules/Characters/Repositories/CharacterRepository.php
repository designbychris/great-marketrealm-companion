<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Contracts\RepositoryInterface;

defined('ABSPATH') || exit;

/**
 * Character Repository.
 *
 * Handles persistence for Character domain models.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class CharacterRepository implements RepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?Character
    {
        return null;
    }

    public function delete(int $id): bool
    {
        return true;
    }

    public function create(Character $character): Character
    {
        return $character;
    }

    public function update(Character $character): Character
    {
        return $character;
    }

    /**
     * Convert database data into a Character model.
     */
    protected function map(array $data): Character
    {
        return new Character(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            race: (string) ($data['race'] ?? ''),
            class: (string) ($data['class'] ?? ''),
            level: (int) ($data['level'] ?? 1),
        );
    }
}
