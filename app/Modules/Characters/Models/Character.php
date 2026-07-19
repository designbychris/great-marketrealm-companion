<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Models;

defined('ABSPATH') || exit;

/**
 * Character.
 *
 * Domain model representing a player character.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class Character
{
    /**
     * Constructor.
     */
    public function __construct(
        protected int $id = 0,
        protected string $name = '',
        protected string $race = '',
        protected string $class = '',
        protected int $level = 1,
    ) {
    }

    /**
     * Character ID.
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Character name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Character race.
     */
    public function race(): string
    {
        return $this->race;
    }

    /**
     * Character class.
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * Character level.
     */
    public function level(): int
    {
        return $this->level;
    }

    /**
     * Display name.
     */
    public function displayName(): string
    {
        return sprintf(
            '%s (Level %d %s)',
            $this->name,
            $this->level,
            $this->class
        );
    }

    /**
     * Determine whether the character is new.
     */
    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
