<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Passive Perception Value Object.
 *
 * Represents a Character's passive awareness.
 *
 * Passive Perception is currently calculated as:
 *
 * 10 + Wisdom modifier
 *
 * Future proficiency, expertise, feats, equipment and
 * magical effects can be incorporated later.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class PassivePerception
{
    /**
     * Lowest supported Passive Perception.
     */
    private const MINIMUM = 0;

    /**
     * Highest supported Passive Perception.
     */
    private const MAXIMUM = 40;

    /**
     * Base Passive Perception.
     */
    private const BASE = 10;

    /**
     * Create a Passive Perception value.
     */
    private function __construct(
        private int $value
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create Passive Perception from an integer.
     */
    public static function fromInt(
        int $value
    ): self {
        return new self($value);
    }

    /**
     * Calculate Passive Perception from Wisdom.
     */
    public static function fromWisdom(
        AbilityScore $wisdom
    ): self {
        return new self(
            self::BASE + $wisdom->modifier()
        );
    }

    /**
     * Return the Passive Perception value.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Determine whether this value equals another.
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
     * Convert Passive Perception to a string.
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
                    'Passive Perception must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $value
                )
            );
        }
    }
}
