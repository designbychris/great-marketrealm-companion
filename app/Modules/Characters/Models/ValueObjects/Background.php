<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Background Value Object.
 *
 * Represents a canonical Character background and the
 * static proficiencies and choices granted by it.
 *
 * The Character's selected languages and tools will later
 * be represented by their own dedicated Value Objects.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Background implements Stringable
{
    /**
     * Supported Character backgrounds.
     *
     * @var array<string,array{
     *     label: string,
     *     skills: array<int,string>,
     *     language_choices: int,
     *     fixed_languages: array<int,string>,
     *     tools: array<int,string>
     * }>
     */
    private const BACKGROUNDS = [
        /*
         * Great Marketrealm backgrounds.
         */
        'market-runner' => [
            'label' => 'Market Runner',
            'skills' => [
                'acrobatics',
                'perception',
            ],
            'language_choices' => 1,
            'fixed_languages' => [],
            'tools' => [
                'land-vehicles',
            ],
        ],

        'shelf-scholar' => [
            'label' => 'Shelf Scholar',
            'skills' => [
                'arcana',
                'history',
            ],
            'language_choices' => 2,
            'fixed_languages' => [],
            'tools' => [
                'calligraphers-supplies',
            ],
        ],

        'waste-warden' => [
            'label' => 'Waste-Warden',
            'skills' => [
                'nature',
                'survival',
            ],
            'language_choices' => 1,
            'fixed_languages' => [],
            'tools' => [
                'herbalism-kit',
            ],
        ],


        /*
         * Canonical optional backgrounds from The Great Marketrealm -
         * Players Handbook. The source states no language grants.
         */
        'crateborn-noble' => [
            'label' => 'Crateborn Noble',
            'skills' => ['persuasion', 'history'],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => ['cartographers-tools'],
        ],
        'backshelf-forager' => [
            'label' => 'Backshelf Forager',
            'skills' => ['survival', 'investigation'],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => ['herbalism-kit'],
        ],
        'discount-bin-survivor' => [
            'label' => 'Discount Bin Survivor',
            'skills' => ['intimidation', 'stealth'],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => ['tinkers-tools'],
        ],
        'cleaners-acolyte' => [
            'label' => 'Cleaner’s Acolyte',
            'skills' => ['arcana', 'religion'],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => ['alchemists-supplies'],
        ],
        'cart-ranger' => [
            'label' => 'Cart Ranger',
            'skills' => ['athletics', 'nature'],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => ['navigators-tools'],
        ],

        /*
         * Standard-compatible backgrounds retained for
         * existing characters and future expansion.
         */
        'guild-artisan' => [
            'label' => 'Guild Artisan',
            'skills' => [
                'insight',
                'persuasion',
            ],
            'language_choices' => 1,
            'fixed_languages' => [],
            'tools' => [
                'artisans-tools',
            ],
        ],

        'folk-hero' => [
            'label' => 'Folk Hero',
            'skills' => [
                'animal-handling',
                'survival',
            ],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => [
                'artisans-tools',
                'land-vehicles',
            ],
        ],

        'sage' => [
            'label' => 'Sage',
            'skills' => [
                'arcana',
                'history',
            ],
            'language_choices' => 2,
            'fixed_languages' => [],
            'tools' => [],
        ],

        'soldier' => [
            'label' => 'Soldier',
            'skills' => [
                'athletics',
                'intimidation',
            ],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => [
                'gaming-set',
                'land-vehicles',
            ],
        ],

        'criminal' => [
            'label' => 'Criminal',
            'skills' => [
                'deception',
                'stealth',
            ],
            'language_choices' => 0,
            'fixed_languages' => [],
            'tools' => [
                'gaming-set',
                'thieves-tools',
            ],
        ],
    ];

    /**
     * Create a Background.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly string $value,
        private readonly ?array $skillSnapshot = null,
        private readonly ?array $toolSnapshot = null,
        private readonly ?string $labelSnapshot = null
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create a Background from a string.
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
     * Rebuild a Background with certified mechanics captured at inscription.
     *
     * @param array<int,string> $skills
     * @param array<int,string> $tools
     */
    public static function fromStringWithMechanics(
        string $value,
        array $skills,
        array $tools,
        ?string $label = null
    ): self {
        $value = self::normalise($value);

        return new self(
            $value,
            SkillProficiencies::proficient($skills)->proficiencies(),
            ToolProficiencies::fromStrings($tools)->values(),
            $label !== null ? trim($label) : null
        );
    }

    /**
     * Return the canonical background identifier.
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
        if ($this->labelSnapshot !== null && $this->labelSnapshot !== '') {
            return $this->labelSnapshot;
        }

        return self::BACKGROUNDS[$this->value]['label'] ?? ucwords(str_replace('-', ' ', preg_replace('/^steward-background-/', '', $this->value)));
    }

    /**
     * Return the skill proficiencies granted
     * by this background.
     */
    public function skillProficiencies(): SkillProficiencies
    {
        return SkillProficiencies::proficient(
            $this->skillSnapshot
                ?? self::BACKGROUNDS[
                    $this->value
                ]['skills']
        );
    }

    /**
     * Return the number of additional languages
     * the Character may choose.
     */
    public function languageChoices(): int
    {
        return self::BACKGROUNDS[
            $this->value
        ]['language_choices'];
    }

    /**
     * Return fixed language identifiers granted
     * by the background.
     *
     * @return array<int,string>
     */
    public function fixedLanguageIdentifiers(): array
    {
        return self::BACKGROUNDS[
            $this->value
        ]['fixed_languages'];
    }

    /**
     * Return tool-proficiency identifiers granted
     * by the background.
     *
     * These identifiers will be converted into the
     * ToolProficiencies Value Object in a later step.
     *
     * @return array<int,string>
     */
    public function toolProficiencyIdentifiers(): array
    {
        return $this->toolSnapshot
            ?? self::BACKGROUNDS[
                $this->value
            ]['tools'];
    }

    /**
     * Determine whether the background grants a
     * particular skill proficiency.
     */
    public function grantsSkillProficiency(
        string $skill
    ): bool {
        return $this
            ->skillProficiencies()
            ->isProficient($skill);
    }

    /**
     * Determine whether the background grants a
     * particular tool proficiency.
     */
    public function grantsToolProficiency(
        string $tool
    ): bool {
        $tool = self::normalise($tool);

        return in_array(
            $tool,
            $this->toolProficiencyIdentifiers(),
            true
        );
    }

    /**
     * Return the certified proficiency snapshot carried by this instance.
     *
     * @return array{skills:array<int,string>,tools:array<int,string>}
     */
    public function mechanicsSnapshot(): array
    {
        return [
            'skills' => $this->skillProficiencies()->proficiencies(),
            'tools' => $this->toolProficiencyIdentifiers(),
        ];
    }

    /**
     * Determine whether this Background equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Determine whether a background identifier
     * is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::BACKGROUNDS
        );
    }

    /**
     * Return every supported Background.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $background
            ): self => new self($background),
            array_keys(self::BACKGROUNDS)
        );
    }

    /**
     * Return every supported background identifier.
     *
     * @return array<int,string>
     */
    public static function identifiers(): array
    {
        return array_keys(
            self::BACKGROUNDS
        );
    }
    
    /**
     * Return every supported background label,
     * keyed by canonical identifier.
     *
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return array_map(
            static fn (
                array $background
            ): string => $background['label'],
            self::BACKGROUNDS
        );
    }

    /**
     * Convert the Background to its canonical identifier.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Normalise primitive input.
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
     * Guard against an unsupported Background.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character background cannot be empty.'
            );
        }

        if (
            ! array_key_exists($value, self::BACKGROUNDS)
            && ($this->skillSnapshot === null || $this->toolSnapshot === null)
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character background "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
