<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character-class value object.
 *
 * Represents a playable Character class and the size
 * of its starting hit die.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterClass implements Stringable
{
    /**
     * Supported Character classes and their hit dice.
     *
     * @var array<string,int>
     */
    private const HIT_DICE = [
        'artificer' => 8,
        'barbarian' => 12,
        'bard' => 8,
        'cleric' => 8,
        'druid' => 8,
        'fighter' => 10,
        'monk' => 8,
        'paladin' => 10,
        'ranger' => 10,
        'rogue' => 8,
        'sorcerer' => 6,
        'warlock' => 8,
        'wizard' => 6,
    ];

    /**
     * Create a Character class.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly string $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Create a Character class from a string.
     *
     * @throws InvalidArgumentException
     */
    public static function fromString(
        string $value
    ): self {
        return new self(
            self::normalise($value)
        );
    }

    /**
     * Return the canonical class identifier.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Return the display name.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * Return the class hit-die size.
     */
    public function hitDie(): int
    {
        return self::HIT_DICE[$this->value];
    }

    /**
     * Calculate first-level maximum hit points.
     */
    public function startingHitPoints(
        AbilityScore $constitution
    ): int {
        return max(
            1,
            $this->hitDie() + $constitution->modifier()
        );
    }

    /**
     * Determine whether this class equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Convert the class to its canonical string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Return every supported Character class.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (string $class): self =>
                new self($class),
            array_keys(self::HIT_DICE)
        );
    }

    /**
     * Normalise class input.
     */
    private static function normalise(
        string $value
    ): string {
        return strtolower(
            trim($value)
        );
    }

    /**
     * Validate the supplied class.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character class cannot be empty.'
            );
        }

        if (! array_key_exists($value, self::HIT_DICE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character class "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
