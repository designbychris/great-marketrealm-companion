<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Speed Value Object.
 *
 * Represents a Character's movement speed in feet.
 *
 * Speeds are expressed in five-foot increments. Future systems
 * may derive a Character's current speed from race, equipment,
 * conditions, spells and temporary effects.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Speed
{
    /**
     * Lowest supported speed.
     */
    private const MINIMUM = 0;

    /**
     * Highest supported speed.
     */
    private const MAXIMUM = 120;

    /**
     * Required speed increment.
     */
    private const INCREMENT = 5;

    /**
     * Standard walking speed.
     */
    private const STANDARD = 30;

    /**
     * Create a Speed.
     */
    private function __construct(
        private int $feet
    ) {
        $this->guardAgainstInvalidValue(
            $feet
        );
    }

    /**
     * Create a Speed from a number of feet.
     */
    public static function fromFeet(
        int $feet
    ): self {
        return new self($feet);
    }

    /**
     * Create the standard walking speed.
     */
    public static function standard(): self
    {
        return new self(
            self::STANDARD
        );
    }

    /**
     * Create a stationary speed.
     */
    public static function stationary(): self
    {
        return new self(0);
    }

    /**
     * Return the speed in feet.
     */
    public function feet(): int
    {
        return $this->feet;
    }

    /**
     * Return the numeric speed value.
     *
     * This mirrors the common value() API used by the
     * Character module's other Value Objects.
     */
    public function value(): int
    {
        return $this->feet;
    }

    /**
     * Increase the speed.
     */
    public function increase(
        int $feet
    ): self {
        $this->guardAgainstInvalidAdjustment(
            $feet
        );

        return new self(
            $this->feet + $feet
        );
    }

    /**
     * Reduce the speed.
     *
     * Speed cannot fall below zero.
     */
    public function reduce(
        int $feet
    ): self {
        $this->guardAgainstInvalidAdjustment(
            $feet
        );

        return new self(
            max(
                self::MINIMUM,
                $this->feet - $feet
            )
        );
    }

    /**
     * Determine whether the Character is able to move.
     */
    public function canMove(): bool
    {
        return $this->feet > 0;
    }

    /**
     * Determine whether this Speed equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->feet === $other->feet;
    }

    /**
     * Return the minimum supported speed.
     */
    public static function minimum(): int
    {
        return self::MINIMUM;
    }

    /**
     * Return the maximum supported speed.
     */
    public static function maximum(): int
    {
        return self::MAXIMUM;
    }

    /**
     * Return the required speed increment.
     */
    public static function increment(): int
    {
        return self::INCREMENT;
    }

    /**
     * Format the speed for display.
     */
    public function formatted(): string
    {
        return sprintf(
            '%d ft',
            $this->feet
        );
    }

    /**
     * Convert the speed to its numeric string value.
     */
    public function __toString(): string
    {
        return (string) $this->feet;
    }

    /**
     * Guard against an unsupported speed.
     */
    private function guardAgainstInvalidValue(
        int $feet
    ): void {
        if (
            $feet < self::MINIMUM
            || $feet > self::MAXIMUM
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Speed must be between %d and %d feet; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $feet
                )
            );
        }

        if ($feet % self::INCREMENT !== 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Speed must use increments of %d feet; received %d.',
                    self::INCREMENT,
                    $feet
                )
            );
        }
    }

    /**
     * Guard against an unsupported adjustment.
     */
    private function guardAgainstInvalidAdjustment(
        int $feet
    ): void {
        if ($feet < 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Speed adjustments cannot be negative; received %d.',
                    $feet
                )
            );
        }

        if ($feet % self::INCREMENT !== 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Speed adjustments must use increments of %d feet; received %d.',
                    self::INCREMENT,
                    $feet
                )
            );
        }
    }
}
