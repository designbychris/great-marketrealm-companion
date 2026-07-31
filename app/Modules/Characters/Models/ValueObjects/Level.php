<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use LogicException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character level value object.
 *
 * Represents a character level between 1 and 20 and provides
 * behaviour relating to character progression.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Level implements Stringable
{
    /**
     * Lowest permitted character level.
     */
    private const MINIMUM = 1;

    /**
     * Highest permitted character level.
     */
    private const MAXIMUM = 20;

    /**
     * Create a character level.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly int $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Create a level from an integer.
     *
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * Create the starting character level.
     */
    public static function one(): self
    {
        return new self(self::MINIMUM);
    }

    /**
     * Return the level as an integer.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Return the next character level.
     *
     * The current object remains unchanged.
     *
     * @throws LogicException
     */
    public function next(): self
    {
        if ($this->isMaximum()) {
            throw new LogicException(
                'A character cannot progress beyond level 20.'
            );
        }

        return new self(
            $this->value + 1
        );
    }

    /**
     * Return the previous character level.
     *
     * The current object remains unchanged.
     *
     * @throws LogicException
     */
    public function previous(): self
    {
        if ($this->isMinimum()) {
            throw new LogicException(
                'A character cannot fall below level 1.'
            );
        }

        return new self(
            $this->value - 1
        );
    }

    /**
     * Determine whether this is the starting level.
     */
    public function isMinimum(): bool
    {
        return $this->value === self::MINIMUM;
    }

    /**
     * Determine whether this is the maximum level.
     */
    public function isMaximum(): bool
    {
        return $this->value === self::MAXIMUM;
    }

    /**
     * Return the proficiency bonus for this level.
     */
    public function proficiencyBonus(): int
    {
        return 2 + intdiv(
            $this->value - 1,
            4
        );
    }

    /**
     * Determine whether this level equals another.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Convert the level to a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Validate the supplied level.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(int $value): void
    {
        if ($value < self::MINIMUM) {
            throw new InvalidArgumentException(
                'A character level cannot be lower than 1.'
            );
        }

        if ($value > self::MAXIMUM) {
            throw new InvalidArgumentException(
                'A character level cannot be higher than 20.'
            );
        }
    }
}
