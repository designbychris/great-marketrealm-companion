<?php

namespace GreatMarketrealmCompanion\Contracts;

defined('ABSPATH') || exit;

/**
 * Repository Interface.
 *
 * Defines the common contract for all repositories.
 *
 * Repositories are responsible for retrieving and
 * persisting domain models whilst hiding the underlying
 * storage implementation.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
interface RepositoryInterface
{
    /**
     * Return all entities.
     *
     * @return array<object>
     */
    public function all(): array;

    /**
     * Find an entity by its identifier.
     *
     * Returns null if the entity cannot be found.
     */
    public function find(int $id): ?object;

    /**
     * Delete an entity.
     */
    public function delete(int $id): bool;
}
