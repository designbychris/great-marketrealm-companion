<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Skill Value Object.
 *
 * Represents one Character skill modifier.
 *
 * A skill is calculated from:
 *
 * - its governing ability modifier;
 * - proficiency bonus when proficient;
 * - twice the proficiency bonus when expertise applies.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Skill
{
    /**
     * Lowest supported skill modifier.
     */
    private const MINIMUM = -20;

    /**
     * Highest supported skill modifier.
     */
    private const MAXIMUM = 40;

    /**
     * Create a Skill.
     */
    private function __construct(
        private int $modifier,
        private bool $proficient,
        private bool $expertise
    ) {
        $this->guardAgainstInvalidModifier(
            $modifier
        );

        $this->guardAgainstInvalidProficiencyState();
    }

    /**
     * Create a Skill from resolved values.
     */
    public static function fromModifier(
        int $modifier,
        bool $proficient = false,
        bool $expertise = false
    ): self {
        return new self(
            modifier: $modifier,
            proficient: $proficient,
            expertise: $expertise
        );
    }

    /**
     * Calculate a Skill from its governing ability.
     */
    public static function fromAbility(
        AbilityScore $ability,
        ProficiencyBonus $proficiencyBonus,
        bool $proficient = false,
        bool $expertise = false
    ): self {
        if ($expertise) {
            $proficient = true;
        }

        $modifier = $ability->modifier();

        if ($expertise) {
            $modifier += (
                $proficiencyBonus->value() * 2
            );
        } elseif ($proficient) {
            $modifier += $proficiencyBonus->value();
        }

        return new self(
            modifier: $modifier,
            proficient: $proficient,
            expertise: $expertise
        );
    }

    /**
     * Return the skill modifier.
     */
    public function modifier(): int
    {
        return $this->modifier;
    }

    /**
     * Return the numeric skill value.
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
     * Determine whether the Character has expertise.
     */
    public function hasExpertise(): bool
    {
        return $this->expertise;
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
     * Determine whether this Skill equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->modifier === $other->modifier
            && $this->proficient === $other->proficient
            && $this->expertise === $other->expertise;
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
     * Convert the Skill to a signed string.
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
                    'Skill modifier must be between %d and %d; received %d.',
                    self::MINIMUM,
                    self::MAXIMUM,
                    $modifier
                )
            );
        }
    }

    /**
     * Ensure expertise cannot exist without proficiency.
     */
    private function guardAgainstInvalidProficiencyState(): void
    {
        if (
            $this->expertise
            && ! $this->proficient
        ) {
            throw new InvalidArgumentException(
                'A Skill with expertise must also be proficient.'
            );
        }
    }
}
