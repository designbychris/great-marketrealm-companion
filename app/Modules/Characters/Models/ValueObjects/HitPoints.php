<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character hit points value object.
 *
 * Represents a character's current, maximum and temporary
 * hit points and provides combat-related behaviour.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class HitPoints implements Stringable
{
    /**
     * Create a hit point value.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly int $current,
        private readonly int $maximum,
        private readonly int $temporary = 0,
    ) {
        $this->guardAgainstInvalidValues(
            $current,
            $maximum,
            $temporary
        );
    }

    /**
     * Create full hit points.
     *
     * Current hit points begin at the supplied maximum.
     */
    public static function full(int $maximum): self
    {
        return new self(
            current: $maximum,
            maximum: $maximum,
            temporary: 0
        );
    }

    /**
     * Recreate an existing hit point state.
     *
     * @throws InvalidArgumentException
     */
    public static function fromValues(
        int $current,
        int $maximum,
        int $temporary = 0,
    ): self {
        return new self(
            current: $current,
            maximum: $maximum,
            temporary: $temporary
        );
    }

    /**
     * Return the current hit points.
     */
    public function current(): int
    {
        return $this->current;
    }

    /**
     * Return the maximum hit points.
     */
    public function maximum(): int
    {
        return $this->maximum;
    }

    /**
     * Return the temporary hit points.
     */
    public function temporary(): int
    {
        return $this->temporary;
    }

    /**
     * Replace mutable play-state hit points while preserving certified maximum.
     *
     * This is used by the Character Ledger during play. Maximum hit points are
     * deliberately not accepted here because they belong to progression.
     */
    public function withLiveState(
        int $current,
        int $temporary
    ): self {
        return new self(
            current: $current,
            maximum: $this->maximum,
            temporary: $temporary
        );
    }

    /**
     * Apply damage and return the resulting hit point state.
     *
     * Temporary hit points absorb damage before current hit points.
     *
     * @throws InvalidArgumentException
     */
    public function takeDamage(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'Damage cannot be negative.'
            );
        }

        $temporaryDamage = min(
            $amount,
            $this->temporary
        );

        $remainingDamage = $amount - $temporaryDamage;

        return new self(
            current: max(
                0,
                $this->current - $remainingDamage
            ),
            maximum: $this->maximum,
            temporary: $this->temporary - $temporaryDamage
        );
    }

    /**
     * Restore hit points and return the resulting state.
     *
     * Healing cannot increase current hit points beyond maximum.
     *
     * @throws InvalidArgumentException
     */
    public function heal(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'Healing cannot be negative.'
            );
        }

        return new self(
            current: min(
                $this->maximum,
                $this->current + $amount
            ),
            maximum: $this->maximum,
            temporary: $this->temporary
        );
    }

    /**
     * Grant temporary hit points.
     *
     * Temporary hit points do not stack. Only the higher value is retained.
     *
     * @throws InvalidArgumentException
     */
    public function grantTemporary(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(
                'Temporary hit points cannot be negative.'
            );
        }

        return new self(
            current: $this->current,
            maximum: $this->maximum,
            temporary: max(
                $this->temporary,
                $amount
            )
        );
    }


/**
 * Increase maximum hit points during level progression.
 */
public function increaseMaximum(int $amount): self
{
    if ($amount < 1) {
        throw new InvalidArgumentException(
            'Maximum hit point growth must be at least 1.'
        );
    }

    return new self(
        current: $this->current + $amount,
        maximum: $this->maximum + $amount,
        temporary: $this->temporary
    );
}

    /**
     * Determine whether the character has at least one hit point.
     */
    public function isConscious(): bool
    {
        return $this->current > 0;
    }

    /**
     * Determine whether current hit points equal maximum hit points.
     */
    public function isAtMaximum(): bool
    {
        return $this->current === $this->maximum;
    }

    /**
     * Determine whether two hit point states are equal.
     */
    public function equals(self $other): bool
    {
        return $this->current === $other->current
            && $this->maximum === $other->maximum
            && $this->temporary === $other->temporary;
    }

    /**
     * Convert the hit point state to a string.
     */
    public function __toString(): string
    {
        if ($this->temporary > 0) {
            return sprintf(
                '%d/%d (+%d temporary)',
                $this->current,
                $this->maximum,
                $this->temporary
            );
        }

        return sprintf(
            '%d/%d',
            $this->current,
            $this->maximum
        );
    }

    /**
     * Validate the supplied hit point state.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValues(
        int $current,
        int $maximum,
        int $temporary,
    ): void {
        if ($maximum < 1) {
            throw new InvalidArgumentException(
                'Maximum hit points must be at least 1.'
            );
        }

        if ($current < 0) {
            throw new InvalidArgumentException(
                'Current hit points cannot be negative.'
            );
        }

        if ($current > $maximum) {
            throw new InvalidArgumentException(
                'Current hit points cannot exceed maximum hit points.'
            );
        }

        if ($temporary < 0) {
            throw new InvalidArgumentException(
                'Temporary hit points cannot be negative.'
            );
        }
    }
}
