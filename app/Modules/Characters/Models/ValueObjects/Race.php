<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable Character race value object.
 *
 * Represents the canonical identity of a playable race
 * within the Great Marketrealm.
 *
 * Detailed racial traits, movement, languages and other
 * gameplay rules remain the responsibility of race
 * definitions and dedicated rule objects.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Race implements Stringable
{
    /**
     * Supported playable races.
     *
     * @var array<string,array{
     *     label: string
     * }>
     */
    private const RACES = [
        'boxfolk' => [
            'label' => 'Boxfolk',
        ],
        'capsicumite' => [
            'label' => 'Capsicumite',
        ],
        'dairyfolk' => [
            'label' => 'Dairyfolk',
        ],
        'drink-folk' => [
            'label' => 'Drinkfolk',
        ],
        'fluffling' => [
            'label' => 'Fluffling',
        ],
        'fructan' => [
            'label' => 'Fructan',
        ],
        'fungifolk' => [
            'label' => 'Fungifolk',
        ],
        'herbfolk' => [
            'label' => 'Herbfolk',
        ],
        'meatfolk' => [
            'label' => 'Meatfolk',
        ],
        'meatkin' => [
            'label' => 'Meatkin',
        ],
        'melonian' => [
            'label' => 'Melonian',
        ],
        'rootkin' => [
            'label' => 'Rootkin',
        ],
        'stalker' => [
            'label' => 'Stalker',
        ],
        'sweetfolk' => [
            'label' => 'Sweetfolk',
        ],
        'vegfolk' => [
            'label' => 'Vegfolk',
        ],
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
        return self::RACES[
            $this->value
        ]['label'];
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
     * Determine whether a race identifier is supported.
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
     * Return every supported race identifier.
     *
     * @return array<int,string>
     */
    public static function identifiers(): array
    {
        return array_keys(
            self::RACES
        );
    }
    
    /**
     * Return every supported race label,
     * keyed by canonical identifier.
     *
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return array_map(
            static fn (
                array $race
            ): string => $race['label'],
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
