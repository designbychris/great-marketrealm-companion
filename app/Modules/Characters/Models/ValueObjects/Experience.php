<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Rules\ExperienceTable;
use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character experience value object.
 *
 * Represents the total experience earned by a character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Experience implements Stringable
{
    /**
     * Create an experience value.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly int $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Create zero experience.
     */
    public static function zero(): self
    {
        return new self(0);
    }

    /**
     * Create experience from an integer.
     *
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * Return the stored experience.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Return a new instance with additional experience.
     *
     * @throws InvalidArgumentException
     */
    public function gain(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'Experience gained cannot be negative.'
            );
        }

        return new self(
            $this->value + $amount
        );
    }

    /**
     * Determine the character's current level.
     */
    public function currentLevel(): Level
    {
        return ExperienceTable::levelForExperience(
            $this->value
        );
    }

    /**
     * Determine whether this experience is sufficient
     * to progress beyond the supplied level.
     */
    public function canLevelUp(Level $level): bool
    {
        $required = ExperienceTable::requiredForNext(
            $level
        );

        if ($required === null) {
            return false;
        }

        return $this->value >= $required;
    }

    /**
     * Determine whether two experience values are equal.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Convert the experience to a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Validate the supplied experience.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(int $value): void
    {
        if ($value < 0) {
            throw new InvalidArgumentException(
                'Experience cannot be negative.'
            );
        }
    }
}
