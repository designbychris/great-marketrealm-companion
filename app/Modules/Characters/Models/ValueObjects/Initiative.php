<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Initiative Value Object.
 *
 * Represents a Character's initiative modifier.
 *
 * Initiative is currently derived from Dexterity.
 * Future bonuses from classes, feats, equipment and
 * magical effects can be incorporated later.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Initiative
{
    /**
     * Lowest supported initiative modifier.
     */
    private const MINIMUM = -10;

    /**
     * Highest supported initiative modifier.
     */
    private const MAXIMUM = 20;

    /**
     * Create an Initiative modifier.
     */
    private function __construct(
        private int $modifier
    ) {
        $this->guardAgainstInvalidModifier(
            $modifier
        );
    }

    /**
     * Create Initiative from a modifier.
     */
    public static function fromModifier(
        int $modifier
    ): self {
        return new self($modifier);
    }

    /**
     * Calculate Initiative from Dexterity.
     */
    public static function fromDexterity(
        AbilityScore $dexterity
    ): self {
        return new self(
            $dexterity->modifier()
        );
    }

    /**
     * Return the initiative modifier.
     */
    public function modifier(): int
    {
        return $this->modifier;
    }

    /**
     * Return the numeric initiative value.
     */
    public function value(): int
    {
        return $this->modifier;
    }

    /**
     * Return the initiative modifier with a sign.
     */
    public function signed(): string
    {
        return $this->modifier >= 0
            ? '+' . $this->modifier
            : (string) $this->modifier;
    }

    /**
     * Determine whether this Initiative equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->modifier === $other->modifier;
    }

    /**
     * Return the minimum supported modifier.
     */
    public static function minimum(): int
    {
        return self::MINIMUM;
    }

    /**
     * Return the maximum supported modifier.
     */
    public static function maximum(): int
    {
        return self::MAXIMUM;
    }

    /**
     * Convert Initiative to its signed display value.
     */
    public function __toString(): string
    {
        return $this->signed();
    }

    /**
     * Guard against an unsupported initiative modifier.
     */
    private function guardAgainstInvalidModifier(
        int $modifier
    ): void {
        if (
            $modifier < self::MINIMUM
            || $modifier > self::MAXIMUM
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Initiative must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $modifier
                )
            );
        }
    }
}
