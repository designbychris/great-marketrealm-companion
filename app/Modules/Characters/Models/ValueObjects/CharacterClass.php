<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable Character Class Value Object.
 *
 * Represents the canonical identity of a playable
 * Character class, its hit-die size and its
 * saving-throw proficiencies.
 *
 * Detailed class features and progression remain the
 * responsibility of class definitions and rules.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterClass implements Stringable
{
    /**
     * Supported Character classes.
     *
     * @var array<string,array{
     *     label: string,
     *     hit_die: int,
     *     saving_throws: array<int,string>
     * }>
     */
    private const CLASSES = [
        /*
         * Great Marketrealm classes.
         */
        'grocer' => [
            'label' => 'Grocer',
            'hit_die' => 8,
            'saving_throws' => [
                'wisdom',
                'charisma',
            ],
        ],

        'cleaver-saint' => [
            'label' => 'Cleaver Saint',
            'hit_die' => 10,
            'saving_throws' => [
                'wisdom',
                'charisma',
            ],
        ],

        /*
         * Standard classes retained for existing characters,
         * future compatibility and homebrew subclasses.
         */
        'artificer' => [
            'label' => 'Artificer',
            'hit_die' => 8,
            'saving_throws' => [
                'constitution',
                'intelligence',
            ],
        ],

        'barbarian' => [
            'label' => 'Barbarian',
            'hit_die' => 12,
            'saving_throws' => [
                'strength',
                'constitution',
            ],
        ],

        'bard' => [
            'label' => 'Bard',
            'hit_die' => 8,
            'saving_throws' => [
                'dexterity',
                'charisma',
            ],
        ],

        'cleric' => [
            'label' => 'Cleric',
            'hit_die' => 8,
            'saving_throws' => [
                'wisdom',
                'charisma',
            ],
        ],

        'druid' => [
            'label' => 'Druid',
            'hit_die' => 8,
            'saving_throws' => [
                'intelligence',
                'wisdom',
            ],
        ],

        'fighter' => [
            'label' => 'Fighter',
            'hit_die' => 10,
            'saving_throws' => [
                'strength',
                'constitution',
            ],
        ],

        'monk' => [
            'label' => 'Monk',
            'hit_die' => 8,
            'saving_throws' => [
                'strength',
                'dexterity',
            ],
        ],

        'paladin' => [
            'label' => 'Paladin',
            'hit_die' => 10,
            'saving_throws' => [
                'wisdom',
                'charisma',
            ],
        ],

        'ranger' => [
            'label' => 'Ranger',
            'hit_die' => 10,
            'saving_throws' => [
                'strength',
                'dexterity',
            ],
        ],

        'rogue' => [
            'label' => 'Rogue',
            'hit_die' => 8,
            'saving_throws' => [
                'dexterity',
                'intelligence',
            ],
        ],

        'sorcerer' => [
            'label' => 'Sorcerer',
            'hit_die' => 6,
            'saving_throws' => [
                'constitution',
                'charisma',
            ],
        ],

        'warlock' => [
            'label' => 'Warlock',
            'hit_die' => 8,
            'saving_throws' => [
                'wisdom',
                'charisma',
            ],
        ],

        'wizard' => [
            'label' => 'Wizard',
            'hit_die' => 6,
            'saving_throws' => [
                'intelligence',
                'wisdom',
            ],
        ],
    ];

    /**
     * Create a Character class.
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
     * Return the display label.
     */
    public function label(): string
    {
        return self::CLASSES[
            $this->value
        ]['label'];
    }

    /**
     * Return the class hit-die size.
     */
    public function hitDie(): int
    {
        return self::CLASSES[
            $this->value
        ]['hit_die'];
    }

    /**
     * Return the class saving-throw proficiencies.
     *
     * @return array<int,string>
     */
    public function savingThrowProficiencies(): array
    {
        return self::CLASSES[
            $this->value
        ]['saving_throws'];
    }

    /**
     * Determine whether this class is proficient in
     * a particular saving throw.
     */
    public function isProficientInSavingThrow(
        string $ability
    ): bool {
        $ability = strtolower(
            trim($ability)
        );

        return in_array(
            $ability,
            $this->savingThrowProficiencies(),
            true
        );
    }

    /**
     * Calculate first-level maximum hit points.
     */
    public function startingHitPoints(
        AbilityScore $constitution
    ): int {
        return max(
            1,
            $this->hitDie()
                + $constitution->modifier()
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
     * Convert the class to its canonical identifier.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Determine whether a class identifier is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::CLASSES
        );
    }

    /**
     * Return every supported Character class.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $class
            ): self => new self($class),
            array_keys(self::CLASSES)
        );
    }

    /**
     * Return every supported Character-class identifier.
     *
     * @return array<int,string>
     */
    public static function identifiers(): array
    {
        return array_keys(
            self::CLASSES
        );
    }
    
    /**
     * Return every supported Character-class label,
     * keyed by canonical identifier.
     *
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return array_map(
            static fn (
                array $class
            ): string => $class['label'],
            self::CLASSES
        );
    }

    /**
     * Normalise class input.
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

        if (! array_key_exists($value, self::CLASSES)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character class "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
