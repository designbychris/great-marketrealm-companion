<?php

namespace GreatMarketrealmCompanion\Contracts;

defined('ABSPATH') || exit;

/**
 * Repository Interface.
 *
 * Defines the contract for repository implementations.
 *
 * Repositories are responsible for persisting and retrieving
 * domain models. They should hide the underlying storage
 * mechanism from the rest of the application.
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
     * Returns null when the entity cannot be found.
     */
    public function find(int $id): ?object;

    /**
     * Persist a new entity.
     */
    public function create(object $entity): object;

    /**
     * Persist changes to an existing entity.
     */
    public function update(object $entity): object;

    /**
     * Delete an entity.
     */
    public function delete(object $entity): bool;
}
