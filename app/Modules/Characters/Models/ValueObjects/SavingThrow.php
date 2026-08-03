<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Saving Throw Value Object.
 *
 * Represents one ability saving throw.
 *
 * A saving throw is calculated from:
 *
 * - the relevant ability modifier;
 * - the Character's proficiency bonus when proficient.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class SavingThrow
{
    /**
     * Lowest supported saving-throw modifier.
     */
    private const MINIMUM = -20;

    /**
     * Highest supported saving-throw modifier.
     */
    private const MAXIMUM = 30;

    /**
     * Create a Saving Throw.
     */
    private function __construct(
        private int $modifier,
        private bool $proficient
    ) {
        $this->guardAgainstInvalidModifier(
            $modifier
        );
    }

    /**
     * Create a Saving Throw from its resolved values.
     */
    public static function fromModifier(
        int $modifier,
        bool $proficient = false
    ): self {
        return new self(
            $modifier,
            $proficient
        );
    }

    /**
     * Calculate a Saving Throw from an ability score.
     */
    public static function fromAbility(
        AbilityScore $ability,
        ProficiencyBonus $proficiencyBonus,
        bool $proficient = false
    ): self {
        $modifier = $ability->modifier();

        if ($proficient) {
            $modifier += $proficiencyBonus->value();
        }

        return new self(
            $modifier,
            $proficient
        );
    }

    /**
     * Return the saving-throw modifier.
     */
    public function modifier(): int
    {
        return $this->modifier;
    }

    /**
     * Return the numeric saving-throw value.
     */
    public function value(): int
    {
        return $this->modifier;
    }

    /**
     * Determine whether the Character is proficient.
     */
    public function isProficient(): bool
    {
        return $this->proficient;
    }

    /**
     * Format the modifier with its sign.
     */
    public function signed(): string
    {
        return $this->modifier >= 0
            ? '+' . $this->modifier
            : (string) $this->modifier;
    }

    /**
     * Determine whether this Saving Throw equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->modifier === $other->modifier
            && $this->proficient === $other->proficient;
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
     * Convert the Saving Throw to a signed string.
     */
    public function __toString(): string
    {
        return $this->signed();
    }

    /**
     * Guard against an unsupported modifier.
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
                    'Saving Throw must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $modifier
                )
            );
        }
    }
}
