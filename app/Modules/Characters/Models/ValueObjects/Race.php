<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character race value object.
 *
 * Represents the canonical identity of a playable race
 * within the Great Marketrealm.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Race implements Stringable
{
    /**
     * Supported playable races.
     *
     * These identifiers are intentionally kept separate from
     * racial traits, ability bonuses and movement rules.
     *
     * @var array<string,string>
     */
    private const RACES = [
        'boxfolk' => 'Boxfolk',
        'capsicumite' => 'Capsicumite',
        'dairyfolk' => 'Dairyfolk',
        'drinkfolk' => 'Drinkfolk',
        'fluffling' => 'Fluffling',
        'fructan' => 'Fructan',
        'fungifolk' => 'Fungifolk',
        'herbfolk' => 'Herbfolk',
        'meatfolk' => 'Meatfolk',
        'melonian' => 'Melonian',
        'rootkin' => 'Rootkin',
        'stalker' => 'Stalker',
        'sweetfolk' => 'Sweetfolk',
        'vegfolk' => 'Vegfolk',
    ];

    /**
     * Create a Race.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly string $value
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create a Race from a string.
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
     * Return the canonical race identifier.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Return the display label.
     */
    public function label(): string
    {
        return self::RACES[$this->value];
    }

    /**
     * Determine whether this Race equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Convert the Race to its canonical identifier.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Return every supported Race.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $race
            ): self => new self($race),
            array_keys(self::RACES)
        );
    }

    /**
     * Determine whether a canonical race identifier is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::RACES
        );
    }

    /**
     * Normalise race input.
     */
    private static function normalise(
        string $value
    ): string {
        $value = strtolower(
            trim($value)
        );

        $value = preg_replace(
            '/[\s_]+/',
            '-',
            $value
        );

        return is_string($value)
            ? trim($value, '-')
            : '';
    }

    /**
     * Validate the supplied race.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character race cannot be empty.'
            );
        }

        if (! array_key_exists($value, self::RACES)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character race "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
