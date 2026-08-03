<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Language Value Object.
 *
 * Represents one canonical language spoken,
 * read or understood by a Character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Language implements Stringable
{
    /**
     * Supported languages.
     *
     * @var array<string,string>
     */
    private const LANGUAGES = [
        /*
         * Common and standard-compatible languages.
         */
        'common' => 'Common',
        'dwarvish' => 'Dwarvish',
        'elvish' => 'Elvish',
        'giant' => 'Giant',
        'gnomish' => 'Gnomish',
        'goblin' => 'Goblin',
        'halfling' => 'Halfling',
        'orc' => 'Orc',

        /*
         * Great Marketrealm languages.
         */
        'fructan' => 'Fructan',
        'vegcant' => 'Vegcant',
        'mycelian' => 'Mycelian',
        'dairy-tongue' => 'Dairy Tongue',
        'meat-speech' => 'Meat Speech',
        'shelf-script' => 'Shelf Script',
    ];

    /**
     * Create a Language.
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
     * Create a Language from a string.
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
     * Return the canonical language identifier.
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
        return self::LANGUAGES[
            $this->value
        ];
    }

    /**
     * Determine whether this Language equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Determine whether a language identifier is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::LANGUAGES
        );
    }

    /**
     * Return every supported Language.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $language
            ): self => new self($language),
            array_keys(self::LANGUAGES)
        );
    }

    /**
     * Convert the Language to its canonical identifier.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Normalise language input.
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
     * Guard against an unsupported language.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character language cannot be empty.'
            );
        }

        if (
            ! array_key_exists(
                $value,
                self::LANGUAGES
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character language "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
