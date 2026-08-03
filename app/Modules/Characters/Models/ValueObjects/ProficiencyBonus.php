<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Proficiency Bonus Value Object.
 *
 * Represents the proficiency bonus granted by a
 * Character's current level.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class ProficiencyBonus
{
    /**
     * Lowest supported proficiency bonus.
     */
    private const MINIMUM = 2;

    /**
     * Highest supported proficiency bonus.
     */
    private const MAXIMUM = 6;

    /**
     * Create a Proficiency Bonus.
     */
    private function __construct(
        private int $value
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create a Proficiency Bonus from an integer.
     */
    public static function fromInt(
        int $value
    ): self {
        return new self($value);
    }

    /**
     * Calculate a Proficiency Bonus from a Character level.
     */
    public static function fromLevel(
        Level $level
    ): self {
        return new self(
            2 + intdiv(
                $level->value() - 1,
                4
            )
        );
    }

    /**
     * Return the proficiency bonus value.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Determine whether this bonus equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Return the minimum supported value.
     */
    public static function minimum(): int
    {
        return self::MINIMUM;
    }

    /**
     * Return the maximum supported value.
     */
    public static function maximum(): int
    {
        return self::MAXIMUM;
    }

    /**
     * Convert the bonus to a signed string.
     */
    public function signed(): string
    {
        return '+' . $this->value;
    }

    /**
     * Convert the bonus to a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Guard against an unsupported value.
     */
    private function guardAgainstInvalidValue(
        int $value
    ): void {
        if (
            $value < self::MINIMUM
            || $value > self::MAXIMUM
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Proficiency Bonus must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $value
                )
            );
        }
    }
}
