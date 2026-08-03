<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Armour Class Value Object.
 *
 * Represents how difficult a Character is to hit.
 *
 * Armour Class is currently limited to values between
 * zero and thirty. Equipment and magical effects may
 * contribute to this value in future stages.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class ArmourClass
{
    /**
     * Lowest supported Armour Class.
     */
    private const MINIMUM = 0;

    /**
     * Highest supported Armour Class.
     */
    private const MAXIMUM = 30;

    /**
     * Base Armour Class when unarmoured.
     */
    private const UNARMOURED_BASE = 10;

    /**
     * Create an Armour Class.
     */
    private function __construct(
        private int $value
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create an Armour Class from an integer.
     */
    public static function fromInt(
        int $value
    ): self {
        return new self($value);
    }

    /**
     * Calculate an unarmoured Armour Class.
     *
     * Unarmoured AC is currently calculated as:
     *
     * 10 + Dexterity modifier
     */
    public static function unarmoured(
        AbilityScore $dexterity
    ): self {
        return new self(
            self::UNARMOURED_BASE
                + $dexterity->modifier()
        );
    }

    /**
     * Return the Armour Class value.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Determine whether this Armour Class equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Return the minimum supported Armour Class.
     */
    public static function minimum(): int
    {
        return self::MINIMUM;
    }

    /**
     * Return the maximum supported Armour Class.
     */
    public static function maximum(): int
    {
        return self::MAXIMUM;
    }

    /**
     * Convert the Armour Class to a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Guard against an unsupported Armour Class.
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
                    'Armour Class must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $value
                )
            );
        }
    }
}
