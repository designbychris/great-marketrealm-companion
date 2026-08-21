<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Tool Proficiency Value Object.
 *
 * Represents one canonical Character tool proficiency.
 *
 * Some identifiers, such as artisans-tools and gaming-set,
 * represent a choice category rather than one concrete tool.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class ToolProficiency implements Stringable
{
    /**
     * Tool proficiency category identifiers.
     */
    public const CATEGORY_ARTISANS_TOOLS =
        'artisans-tools';

    public const CATEGORY_GAMING_SET =
        'gaming-set';

    /**
     * Supported tool proficiencies.
     *
     * @var array<string,array{
     *     label: string,
     *     category: string|null,
     *     choice: bool
     * }>
     */
    private const TOOLS = [
        /*
         * Choice categories.
         */
        self::CATEGORY_ARTISANS_TOOLS => [
            'label' => "Artisan's Tools",
            'category' => null,
            'choice' => true,
        ],

        self::CATEGORY_GAMING_SET => [
            'label' => 'Gaming Set',
            'category' => null,
            'choice' => true,
        ],

        /*
         * General tools and vehicles.
         */
        'alchemists-supplies' => [
            'label' => "Alchemist's Supplies",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'calligraphers-supplies' => [
            'label' => "Calligrapher's Supplies",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'cartographers-tools' => [
            'label' => "Cartographer's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'herbalism-kit' => [
            'label' => 'Herbalism Kit',
            'category' => null,
            'choice' => false,
        ],

        'land-vehicles' => [
            'label' => 'Land Vehicles',
            'category' => null,
            'choice' => false,
        ],

        'navigators-tools' => [
            'label' => "Navigator's Tools",
            'category' => null,
            'choice' => false,
        ],

        'thieves-tools' => [
            'label' => "Thieves' Tools",
            'category' => null,
            'choice' => false,
        ],

        /*
         * Artisan's tools.
         */
        'brewers-supplies' => [
            'label' => "Brewer's Supplies",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'carpenters-tools' => [
            'label' => "Carpenter's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'cobblers-tools' => [
            'label' => "Cobbler's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'cooks-utensils' => [
            'label' => "Cook's Utensils",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'glassblowers-tools' => [
            'label' => "Glassblower's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'jewelers-tools' => [
            'label' => "Jeweler's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'leatherworkers-tools' => [
            'label' => "Leatherworker's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'masons-tools' => [
            'label' => "Mason's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'painters-supplies' => [
            'label' => "Painter's Supplies",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'potters-tools' => [
            'label' => "Potter's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'smiths-tools' => [
            'label' => "Smith's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'tinkers-tools' => [
            'label' => "Tinker's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'weavers-tools' => [
            'label' => "Weaver's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        'woodcarvers-tools' => [
            'label' => "Woodcarver's Tools",
            'category' => self::CATEGORY_ARTISANS_TOOLS,
            'choice' => false,
        ],

        /*
         * Gaming sets.
         */
        'dice-set' => [
            'label' => 'Dice Set',
            'category' => self::CATEGORY_GAMING_SET,
            'choice' => false,
        ],

        'dragonchess-set' => [
            'label' => 'Dragonchess Set',
            'category' => self::CATEGORY_GAMING_SET,
            'choice' => false,
        ],

        'playing-card-set' => [
            'label' => 'Playing Card Set',
            'category' => self::CATEGORY_GAMING_SET,
            'choice' => false,
        ],

        'three-dragon-ante-set' => [
            'label' => 'Three-Dragon Ante Set',
            'category' => self::CATEGORY_GAMING_SET,
            'choice' => false,
        ],
    ];

    /**
     * Create a Tool Proficiency.
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
     * Create a Tool Proficiency from a string.
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
     * Return the canonical tool identifier.
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
        return self::TOOLS[
            $this->value
        ]['label'];
    }

    /**
     * Return the parent category identifier.
     *
     * Concrete artisan tools return artisans-tools.
     * Concrete gaming sets return gaming-set.
     */
    public function category(): ?string
    {
        return self::TOOLS[
            $this->value
        ]['category'];
    }

    /**
     * Determine whether this identifier represents
     * a choice category rather than a concrete tool.
     */
    public function isChoiceCategory(): bool
    {
        return self::TOOLS[
            $this->value
        ]['choice'];
    }

    /**
     * Determine whether this is a concrete tool.
     */
    public function isConcrete(): bool
    {
        return ! $this->isChoiceCategory();
    }

    /**
     * Determine whether the proficiency belongs
     * to a particular category.
     */
    public function belongsTo(
        string $category
    ): bool {
        $category = self::normalise(
            $category
        );

        return $this->category() === $category;
    }

    /**
     * Determine whether this proficiency is one
     * of the concrete Artisan's Tools.
     */
    public function isArtisansTool(): bool
    {
        return $this->belongsTo(
            self::CATEGORY_ARTISANS_TOOLS
        );
    }

    /**
     * Determine whether this proficiency is one
     * of the concrete Gaming Sets.
     */
    public function isGamingSet(): bool
    {
        return $this->belongsTo(
            self::CATEGORY_GAMING_SET
        );
    }

    /**
     * Determine whether this Tool Proficiency
     * equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Determine whether a tool identifier
     * is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::TOOLS
        );
    }

    /**
     * Return every supported Tool Proficiency.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $tool
            ): self => new self($tool),
            array_keys(self::TOOLS)
        );
    }

    /**
     * Return every concrete Artisan's Tool.
     *
     * @return array<int,self>
     */
    public static function artisansTools(): array
    {
        return array_values(
            array_filter(
                self::all(),
                static fn (
                    self $tool
                ): bool => $tool->isArtisansTool()
            )
        );
    }

    /**
     * Return every concrete Gaming Set.
     *
     * @return array<int,self>
     */
    public static function gamingSets(): array
    {
        return array_values(
            array_filter(
                self::all(),
                static fn (
                    self $tool
                ): bool => $tool->isGamingSet()
            )
        );
    }

    /**
     * Convert the proficiency to its canonical identifier.
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

        $value = str_replace(
            ["'", '’'],
            '',
            $value
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
     * Guard against an unsupported tool.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character tool proficiency cannot be empty.'
            );
        }

        if (
            ! array_key_exists(
                $value,
                self::TOOLS
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character tool proficiency "%s" is not supported.',
                    $value
                )
            );
        }
    }
}
