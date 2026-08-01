<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use LogicException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable ability score value object.
 *
 * Represents a single ability score and its associated modifier.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class AbilityScore implements Stringable
{
    /**
     * Lowest permitted ability score.
     */
    private const MINIMUM = 1;

    /**
     * Highest permitted ability score.
     */
    private const MAXIMUM = 30;

    /**
     * Create an ability score.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly int $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Create an ability score from an integer.
     *
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * Create the standard average ability score.
     */
    public static function average(): self
    {
        return new self(10);
    }

    /**
     * Return the raw ability score.
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Return the ability modifier.
     */
    public function modifier(): int
    {
        return (int) floor(
            ($this->value - 10) / 2
        );
    }

    /**
     * Return a new score increased by the supplied amount.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function increase(int $amount = 1): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'An ability score increase cannot be negative.'
            );
        }

        $newValue = $this->value + $amount;

        if ($newValue > self::MAXIMUM) {
            throw new LogicException(
                'An ability score cannot exceed 30.'
            );
        }

        return new self($newValue);
    }

    /**
     * Return a new score decreased by the supplied amount.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function decrease(int $amount = 1): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'An ability score decrease cannot be negative.'
            );
        }

        $newValue = $this->value - $amount;

        if ($newValue < self::MINIMUM) {
            throw new LogicException(
                'An ability score cannot fall below 1.'
            );
        }

        return new self($newValue);
    }

    /**
     * Determine whether this is the minimum ability score.
     */
    public function isMinimum(): bool
    {
        return $this->value === self::MINIMUM;
    }

    /**
     * Determine whether this is the maximum ability score.
     */
    public function isMaximum(): bool
    {
        return $this->value === self::MAXIMUM;
    }

    /**
     * Determine whether this score equals another.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Convert the ability score to a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Validate the supplied ability score.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(int $value): void
    {
        if ($value < self::MINIMUM) {
            throw new InvalidArgumentException(
                'An ability score cannot be lower than 1.'
            );
        }

        if ($value > self::MAXIMUM) {
            throw new InvalidArgumentException(
                'An ability score cannot be higher than 30.'
            );
        }
    }
}
