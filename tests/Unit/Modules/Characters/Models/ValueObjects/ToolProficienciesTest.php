<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolProficienciesTest extends TestCase
{
    public function testCreatesAnEmptyCollection(): void
    {
        $tools = ToolProficiencies::none();

        self::assertSame(
            [],
            $tools->all()
        );

        self::assertSame(
            [],
            $tools->values()
        );

        self::assertTrue(
            $tools->isEmpty()
        );

        self::assertSame(
            0,
            $tools->count()
        );
    }

    public function testCreatesCollectionFromToolValues(): void
    {
        $tools = ToolProficiencies::fromTools(
            ToolProficiency::fromString(
                'herbalism-kit'
            ),
            ToolProficiency::fromString(
                'land-vehicles'
            )
        );

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
            ],
            $tools->values()
        );

        self::assertContainsOnlyInstancesOf(
            ToolProficiency::class,
            $tools->all()
        );
    }

    public function testCreatesCollectionFromStrings(): void
    {
        self::assertSame(
            [
                'land-vehicles',
                'thieves-tools',
            ],
            ToolProficiencies::fromStrings([
                'land-vehicles',
                'thieves-tools',
            ])->values()
        );
    }

    public function testNormalisesStringIdentifiers(): void
    {
        self::assertSame(
            [
                'cooks-utensils',
                'smiths-tools',
            ],
            ToolProficiencies::fromStrings([
                " Cook's Utensils ",
                'SMITHS_TOOLS',
            ])->values()
        );
    }

    public function testRemovesDuplicateTools(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'smiths-tools',
            "Smith's Tools",
            ' smiths_tools ',
        ]);

        self::assertSame(
            ['smiths-tools'],
            $tools->values()
        );

        self::assertSame(
            1,
            $tools->count()
        );
    }

    public function testReturnsToolsInCanonicalOrder(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'dice-set',
            'smiths-tools',
            'artisans-tools',
            'herbalism-kit',
        ]);

        self::assertSame(
            [
                'artisans-tools',
                'herbalism-kit',
                'smiths-tools',
                'dice-set',
            ],
            $tools->values()
        );
    }

    public function testDeterminesWhetherToolExistsUsingString(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'herbalism-kit',
            'land-vehicles',
        ]);

        self::assertTrue(
            $tools->has(
                'herbalism-kit'
            )
        );

        self::assertFalse(
            $tools->has(
                'thieves-tools'
            )
        );
    }

    public function testDeterminesWhetherToolExistsUsingValueObject(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'thieves-tools',
        ]);

        self::assertTrue(
            $tools->has(
                ToolProficiency::fromString(
                    "Thieves' Tools"
                )
            )
        );
    }

    public function testAddsToolImmutably(): void
    {
        $original =
            ToolProficiencies::fromStrings([
                'herbalism-kit',
            ]);

        $updated = $original->add(
            'land-vehicles'
        );

        self::assertSame(
            ['herbalism-kit'],
            $original->values()
        );

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
            ],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testAddsToolUsingValueObject(): void
    {
        $updated = ToolProficiencies::none()
            ->add(
                ToolProficiency::fromString(
                    'smiths-tools'
                )
            );

        self::assertSame(
            ['smiths-tools'],
            $updated->values()
        );
    }

    public function testAddingExistingToolReturnsSameCollection(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'smiths-tools',
        ]);

        self::assertSame(
            $tools,
            $tools->add(
                "Smith's Tools"
            )
        );
    }

    public function testRemovesToolImmutably(): void
    {
        $original =
            ToolProficiencies::fromStrings([
                'herbalism-kit',
                'land-vehicles',
            ]);

        $updated = $original->remove(
            'herbalism-kit'
        );

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
            ],
            $original->values()
        );

        self::assertSame(
            ['land-vehicles'],
            $updated->values()
        );
    }

    public function testRemovingMissingToolReturnsSameCollection(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'herbalism-kit',
        ]);

        self::assertSame(
            $tools,
            $tools->remove(
                'land-vehicles'
            )
        );
    }

    public function testMergesCollections(): void
    {
        $first = ToolProficiencies::fromStrings([
            'herbalism-kit',
            'land-vehicles',
        ]);

        $second = ToolProficiencies::fromStrings([
            'smiths-tools',
            'dice-set',
        ]);

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
                'smiths-tools',
                'dice-set',
            ],
            $first->merge(
                $second
            )->values()
        );
    }

    public function testMergeRemovesDuplicates(): void
    {
        $first = ToolProficiencies::fromStrings([
            'herbalism-kit',
            'land-vehicles',
        ]);

        $second = ToolProficiencies::fromStrings([
            'land-vehicles',
            'thieves-tools',
        ]);

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
                'thieves-tools',
            ],
            $first->merge(
                $second
            )->values()
        );
    }

    public function testMergeDoesNotMutateSources(): void
    {
        $first = ToolProficiencies::fromStrings([
            'herbalism-kit',
        ]);

        $second = ToolProficiencies::fromStrings([
            'land-vehicles',
        ]);

        $first->merge($second);

        self::assertSame(
            ['herbalism-kit'],
            $first->values()
        );

        self::assertSame(
            ['land-vehicles'],
            $second->values()
        );
    }

    public function testReturnsConcreteArtisansTools(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'artisans-tools',
            'smiths-tools',
            'cooks-utensils',
            'herbalism-kit',
        ]);

        self::assertSame(
            [
                'cooks-utensils',
                'smiths-tools',
            ],
            array_map(
                static fn (
                    ToolProficiency $tool
                ): string => $tool->value(),
                $tools->artisansTools()
            )
        );
    }

    public function testReturnsConcreteGamingSets(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'gaming-set',
            'dice-set',
            'playing-card-set',
            'thieves-tools',
        ]);

        self::assertSame(
            [
                'dice-set',
                'playing-card-set',
            ],
            array_map(
                static fn (
                    ToolProficiency $tool
                ): string => $tool->value(),
                $tools->gamingSets()
            )
        );
    }

    public function testDetectsUnresolvedChoiceCategories(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'artisans-tools',
            'gaming-set',
            'land-vehicles',
        ]);

        self::assertTrue(
            $tools->hasUnresolvedChoices()
        );

        self::assertSame(
            [
                'artisans-tools',
                'gaming-set',
            ],
            array_map(
                static fn (
                    ToolProficiency $tool
                ): string => $tool->value(),
                $tools->unresolvedChoices()
            )
        );
    }

    public function testConcreteToolsHaveNoUnresolvedChoices(): void
    {
        $tools = ToolProficiencies::fromStrings([
            'smiths-tools',
            'dice-set',
        ]);

        self::assertFalse(
            $tools->hasUnresolvedChoices()
        );

        self::assertSame(
            [],
            $tools->unresolvedChoices()
        );
    }

    public function testCountsToolProficiencies(): void
    {
        self::assertSame(
            3,
            ToolProficiencies::fromStrings([
                'herbalism-kit',
                'land-vehicles',
                'thieves-tools',
            ])->count()
        );
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first = ToolProficiencies::fromStrings([
            'herbalism-kit',
            'land-vehicles',
        ]);

        $second = ToolProficiencies::fromStrings([
            'land-vehicles',
            'herbalism-kit',
        ]);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCollectionsAreNotEqual(): void
    {
        self::assertFalse(
            ToolProficiencies::fromStrings([
                'herbalism-kit',
            ])->equals(
                ToolProficiencies::fromStrings([
                    'land-vehicles',
                ])
            )
        );
    }

    public function testEmptyCollectionsAreEqual(): void
    {
        self::assertTrue(
            ToolProficiencies::none()->equals(
                ToolProficiencies::none()
            )
        );
    }

    public function testRejectsNonStringIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Character tool proficiency identifiers must be strings.'
        );

        ToolProficiencies::fromStrings([
            123,
        ]);
    }

    public function testRejectsUnsupportedIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ToolProficiencies::fromStrings([
            'sandwich-press',
        ]);
    }

    public function testHasRejectsUnsupportedIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ToolProficiencies::none()->has(
            'sandwich-press'
        );
    }

    public function testAddRejectsUnsupportedIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ToolProficiencies::none()->add(
            'sandwich-press'
        );
    }

    public function testRemoveRejectsUnsupportedIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ToolProficiencies::none()->remove(
            'sandwich-press'
        );
    }

    public function testCollectionIsImmutable(): void
    {
        $original = ToolProficiencies::fromStrings([
            'herbalism-kit',
        ]);

        $withVehicle = $original->add(
            'land-vehicles'
        );

        $withoutHerbalism =
            $withVehicle->remove(
                'herbalism-kit'
            );

        self::assertSame(
            ['herbalism-kit'],
            $original->values()
        );

        self::assertSame(
            [
                'herbalism-kit',
                'land-vehicles',
            ],
            $withVehicle->values()
        );

        self::assertSame(
            ['land-vehicles'],
            $withoutHerbalism->values()
        );
    }
}
