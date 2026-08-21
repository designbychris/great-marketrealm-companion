<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ToolProficiencyTest extends TestCase
{
    #[DataProvider('supportedToolProvider')]
    public function testCanBeCreatedFromSupportedTool(
        string $input,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            ToolProficiency::fromString(
                $input
            )->value()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function supportedToolProvider(): array
    {
        return [
            'artisans tools' => [
                'artisans-tools',
                'artisans-tools',
            ],
            'gaming set' => [
                'gaming-set',
                'gaming-set',
            ],
            'calligraphers supplies' => [
                'calligraphers-supplies',
                'calligraphers-supplies',
            ],
            'herbalism kit' => [
                'herbalism-kit',
                'herbalism-kit',
            ],
            'land vehicles' => [
                'land-vehicles',
                'land-vehicles',
            ],
            'thieves tools' => [
                'thieves-tools',
                'thieves-tools',
            ],
            'brewers supplies' => [
                'brewers-supplies',
                'brewers-supplies',
            ],
            'carpenters tools' => [
                'carpenters-tools',
                'carpenters-tools',
            ],
            'cobblers tools' => [
                'cobblers-tools',
                'cobblers-tools',
            ],
            'cooks utensils' => [
                'cooks-utensils',
                'cooks-utensils',
            ],
            'glassblowers tools' => [
                'glassblowers-tools',
                'glassblowers-tools',
            ],
            'jewelers tools' => [
                'jewelers-tools',
                'jewelers-tools',
            ],
            'leatherworkers tools' => [
                'leatherworkers-tools',
                'leatherworkers-tools',
            ],
            'masons tools' => [
                'masons-tools',
                'masons-tools',
            ],
            'painters supplies' => [
                'painters-supplies',
                'painters-supplies',
            ],
            'potters tools' => [
                'potters-tools',
                'potters-tools',
            ],
            'smiths tools' => [
                'smiths-tools',
                'smiths-tools',
            ],
            'tinkers tools' => [
                'tinkers-tools',
                'tinkers-tools',
            ],
            'weavers tools' => [
                'weavers-tools',
                'weavers-tools',
            ],
            'woodcarvers tools' => [
                'woodcarvers-tools',
                'woodcarvers-tools',
            ],
            'dice set' => [
                'dice-set',
                'dice-set',
            ],
            'dragonchess set' => [
                'dragonchess-set',
                'dragonchess-set',
            ],
            'playing card set' => [
                'playing-card-set',
                'playing-card-set',
            ],
            'three dragon ante set' => [
                'three-dragon-ante-set',
                'three-dragon-ante-set',
            ],
        ];
    }

    public function testNormalisesUppercaseInput(): void
    {
        self::assertSame(
            'herbalism-kit',
            ToolProficiency::fromString(
                'HERBALISM-KIT'
            )->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        self::assertSame(
            'land-vehicles',
            ToolProficiency::fromString(
                '  land-vehicles  '
            )->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        self::assertSame(
            'smiths-tools',
            ToolProficiency::fromString(
                'Smiths Tools'
            )->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        self::assertSame(
            'cooks-utensils',
            ToolProficiency::fromString(
                'cooks_utensils'
            )->value()
        );
    }

    public function testNormalisesApostrophes(): void
    {
        self::assertSame(
            'thieves-tools',
            ToolProficiency::fromString(
                "Thieves' Tools"
            )->value()
        );

        self::assertSame(
            'smiths-tools',
            ToolProficiency::fromString(
                "Smith’s Tools"
            )->value()
        );
    }

    #[DataProvider('labelProvider')]
    public function testReturnsCorrectLabel(
        string $tool,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            ToolProficiency::fromString(
                $tool
            )->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'artisans tools' => [
                'artisans-tools',
                "Artisan's Tools",
            ],
            'gaming set' => [
                'gaming-set',
                'Gaming Set',
            ],
            'calligraphers supplies' => [
                'calligraphers-supplies',
                "Calligrapher's Supplies",
            ],
            'herbalism kit' => [
                'herbalism-kit',
                'Herbalism Kit',
            ],
            'land vehicles' => [
                'land-vehicles',
                'Land Vehicles',
            ],
            'thieves tools' => [
                'thieves-tools',
                "Thieves' Tools",
            ],
            'brewers supplies' => [
                'brewers-supplies',
                "Brewer's Supplies",
            ],
            'cooks utensils' => [
                'cooks-utensils',
                "Cook's Utensils",
            ],
            'smiths tools' => [
                'smiths-tools',
                "Smith's Tools",
            ],
            'dice set' => [
                'dice-set',
                'Dice Set',
            ],
            'dragonchess set' => [
                'dragonchess-set',
                'Dragonchess Set',
            ],
        ];
    }

    public function testChoiceCategoryIdentifiersAreRecognised(): void
    {
        self::assertTrue(
            ToolProficiency::fromString(
                'artisans-tools'
            )->isChoiceCategory()
        );

        self::assertTrue(
            ToolProficiency::fromString(
                'gaming-set'
            )->isChoiceCategory()
        );
    }

    public function testConcreteToolsAreNotChoiceCategories(): void
    {
        self::assertFalse(
            ToolProficiency::fromString(
                'smiths-tools'
            )->isChoiceCategory()
        );

        self::assertTrue(
            ToolProficiency::fromString(
                'smiths-tools'
            )->isConcrete()
        );
    }

    public function testConcreteArtisansToolHasCorrectCategory(): void
    {
        $tool = ToolProficiency::fromString(
            'cooks-utensils'
        );

        self::assertSame(
            ToolProficiency::CATEGORY_ARTISANS_TOOLS,
            $tool->category()
        );

        self::assertTrue(
            $tool->isArtisansTool()
        );

        self::assertFalse(
            $tool->isGamingSet()
        );
    }

    public function testConcreteGamingSetHasCorrectCategory(): void
    {
        $tool = ToolProficiency::fromString(
            'dice-set'
        );

        self::assertSame(
            ToolProficiency::CATEGORY_GAMING_SET,
            $tool->category()
        );

        self::assertTrue(
            $tool->isGamingSet()
        );

        self::assertFalse(
            $tool->isArtisansTool()
        );
    }

    public function testGeneralToolHasNoCategory(): void
    {
        $tool = ToolProficiency::fromString(
            'herbalism-kit'
        );

        self::assertNull(
            $tool->category()
        );

        self::assertFalse(
            $tool->isArtisansTool()
        );

        self::assertFalse(
            $tool->isGamingSet()
        );
    }

    public function testBelongsToNormalisesCategoryIdentifier(): void
    {
        self::assertTrue(
            ToolProficiency::fromString(
                'smiths-tools'
            )->belongsTo(
                ' Artisan’s Tools '
            )
        );
    }

    public function testReportsSupportedTool(): void
    {
        self::assertTrue(
            ToolProficiency::supports(
                "Cook's Utensils"
            )
        );

        self::assertTrue(
            ToolProficiency::supports(
                'playing_card_set'
            )
        );
    }

    public function testDoesNotSupportUnknownTool(): void
    {
        self::assertFalse(
            ToolProficiency::supports(
                'sandwich-press'
            )
        );
    }

    public function testRejectsEmptyTool(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Character tool proficiency cannot be empty.'
        );

        ToolProficiency::fromString('');
    }

    public function testRejectsWhitespaceOnlyTool(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ToolProficiency::fromString('   ');
    }

    public function testRejectsUnsupportedTool(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character tool proficiency "sandwich-press" is not supported.'
        );

        ToolProficiency::fromString(
            'Sandwich Press'
        );
    }

    public function testEqualToolsAreEqual(): void
    {
        self::assertTrue(
            ToolProficiency::fromString(
                'smiths-tools'
            )->equals(
                ToolProficiency::fromString(
                    " Smith's Tools "
                )
            )
        );
    }

    public function testDifferentToolsAreNotEqual(): void
    {
        self::assertFalse(
            ToolProficiency::fromString(
                'smiths-tools'
            )->equals(
                ToolProficiency::fromString(
                    'cooks-utensils'
                )
            )
        );
    }

    public function testConvertsToCanonicalString(): void
    {
        self::assertSame(
            'thieves-tools',
            (string) ToolProficiency::fromString(
                "Thieves' Tools"
            )
        );
    }

    public function testReturnsEverySupportedTool(): void
    {
        $tools = ToolProficiency::all();

        self::assertCount(
            27,
            $tools
        );

        self::assertContainsOnlyInstancesOf(
            ToolProficiency::class,
            $tools
        );
    }

    public function testReturnsAllConcreteArtisansTools(): void
    {
        $values = array_map(
            static fn (
                ToolProficiency $tool
            ): string => $tool->value(),
            ToolProficiency::artisansTools()
        );

        self::assertSame(
            [
                'alchemists-supplies',
                'calligraphers-supplies',
                'cartographers-tools',
                'brewers-supplies',
                'carpenters-tools',
                'cobblers-tools',
                'cooks-utensils',
                'glassblowers-tools',
                'jewelers-tools',
                'leatherworkers-tools',
                'masons-tools',
                'painters-supplies',
                'potters-tools',
                'smiths-tools',
                'tinkers-tools',
                'weavers-tools',
                'woodcarvers-tools',
            ],
            $values
        );
    }

    public function testReturnsAllConcreteGamingSets(): void
    {
        $values = array_map(
            static fn (
                ToolProficiency $tool
            ): string => $tool->value(),
            ToolProficiency::gamingSets()
        );

        self::assertSame(
            [
                'dice-set',
                'dragonchess-set',
                'playing-card-set',
                'three-dragon-ante-set',
            ],
            $values
        );
    }

    public function testAllToolsHaveUniqueIdentifiers(): void
    {
        $values = array_map(
            static fn (
                ToolProficiency $tool
            ): string => $tool->value(),
            ToolProficiency::all()
        );

        self::assertSame(
            $values,
            array_values(
                array_unique($values)
            )
        );
    }

    public function testToolProficiencyIsImmutable(): void
    {
        $smithsTools =
            ToolProficiency::fromString(
                'smiths-tools'
            );

        $cooksUtensils =
            ToolProficiency::fromString(
                'cooks-utensils'
            );

        self::assertSame(
            'smiths-tools',
            $smithsTools->value()
        );

        self::assertSame(
            'cooks-utensils',
            $cooksUtensils->value()
        );

        self::assertNotSame(
            $smithsTools,
            $cooksUtensils
        );
    }
}
