<?php

namespace GreatMarketrealmCompanion\Repositories;

defined('ABSPATH') || exit;

/**
 * Character Repository
 *
 * Responsible for retrieving character data.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.1
 */
class CharacterRepository
{
    /**
     * Database table.
     *
     * @var string
     */
    protected string $table;

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix . 'gmrc_characters';
    }

    /**
     * Get all characters belonging to a user.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getByUser(int $userId): array
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$this->table}
                WHERE user_id = %d
                ORDER BY created_at DESC
                ",
                $userId
            ),
            ARRAY_A
        );

        return $results ?: [];
    }

    /**
     * Get a character by ID.
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        global $wpdb;

        $character = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$this->table}
                WHERE id = %d
                LIMIT 1
                ",
                $id
            ),
            ARRAY_A
        );

        return $character ?: null;
    }

    /**
     * Determine whether a character exists.
     *
     * @param int $id
     *
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->getById($id) !== null;
    }

    /**
     * Count a user's characters.
     *
     * @param int $userId
     *
     * @return int
     */
    public function countByUser(int $userId): int
    {
        return count($this->getByUser($userId));
    }
}
