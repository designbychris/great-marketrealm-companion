<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\DruidProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\DruidSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DruidCallingRegressionTest extends TestCase
{
    public function testDruidUsesSpecialistProgressionDefinition(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('druid'),
            2
        );

        self::assertSame(
            'druid',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelOneFoundationsRemainDruidicAndSpellcasting(): void
    {
        $foundations = (
            new DruidProgression()
        )->foundations(
            CharacterClass::fromString('druid')
        );

        self::assertSame(
            [
                'druidic',
                'spellcasting',
            ],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testLevelTwoIntroducesWildShapeAndDruidCircle(): void
    {
        $entry = (
            new DruidProgression()
        )->forLevel(
            CharacterClass::fromString('druid'),
            2
        );

        self::assertSame(
            [
                'wild-shape',
                'druid-circle',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testWildShapeImprovesAtFourAndEight(): void
    {
        $progression =
            new DruidProgression();

        foreach ([4, 8] as $level) {
            self::assertContains(
                'wild-shape-improvement',
                array_column(
                    $progression
                        ->forLevel(
                            CharacterClass::fromString('druid'),
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testHighLevelDruidMilestonesRemainAtEighteenAndTwenty(): void
    {
        $progression =
            new DruidProgression();
        $druid =
            CharacterClass::fromString('druid');

        self::assertSame(
            [
                'timeless-body',
                'beast-spells',
            ],
            array_column(
                $progression
                    ->forLevel(
                        $druid,
                        18
                    )['automatic'],
                'key'
            )
        );

        self::assertSame(
            ['archdruid'],
            array_column(
                $progression
                    ->forLevel(
                        $druid,
                        20
                    )['automatic'],
                'key'
            )
        );
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression =
            new DruidProgression();
        $druid =
            CharacterClass::fromString('druid');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $druid,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testCircleGiftMilestonesRemainDelegatedForLaterSlice(): void
    {
        $progression =
            new DruidProgression();
        $druid =
            CharacterClass::fromString('druid');

        foreach ([6, 10, 14] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $druid,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testDruidUsesPreparedSpellFullCasterModel(): void
    {
        $entry = (
            new DruidSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('druid'),
            2
        );

        self::assertSame(
            'prepared-spells',
            $entry['model']
        );

        self::assertNull(
            $entry['spells_known']
        );

        self::assertSame(
            0,
            $entry['spells_learned']
        );

        self::assertSame(
            'druid-level + wisdom-modifier',
            $entry['spells_prepared_formula']
        );

        self::assertSame(
            1,
            $entry['minimum_spells_prepared']
        );
    }

    public function testDruidCantripProgressionRemainsTwoThreeFour(): void
    {
        $definition =
            new DruidSpellcastingProgression();
        $druid =
            CharacterClass::fromString('druid');

        self::assertSame(
            2,
            $definition
                ->forLevel(
                    $druid,
                    2
                )['cantrips_known']
        );

        self::assertSame(
            3,
            $definition
                ->forLevel(
                    $druid,
                    4
                )['cantrips_known']
        );

        self::assertSame(
            4,
            $definition
                ->forLevel(
                    $druid,
                    10
                )['cantrips_known']
        );
    }

    public function testCantripGainsOnlyOccurAtFourAndTenAfterLevelOne(): void
    {
        $definition =
            new DruidSpellcastingProgression();
        $druid =
            CharacterClass::fromString('druid');

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $druid,
                    4
                )['cantrips_learned']
        );

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $druid,
                    10
                )['cantrips_learned']
        );

        self::assertSame(
            0,
            $definition
                ->forLevel(
                    $druid,
                    11
                )['cantrips_learned']
        );
    }

    public function testDruidFullCastingReachesNinthCircleAtSeventeen(): void
    {
        $definition =
            new DruidSpellcastingProgression();
        $druid =
            CharacterClass::fromString('druid');

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $druid,
                    2
                )['maximum_spell_level']
        );

        self::assertSame(
            5,
            $definition
                ->forLevel(
                    $druid,
                    9
                )['maximum_spell_level']
        );

        self::assertSame(
            9,
            $definition
                ->forLevel(
                    $druid,
                    17
                )['maximum_spell_level']
        );
    }

    public function testSpellcastingCatalogueRecognisesDruid(): void
    {
        $catalogue =
            new SpellcastingProgressionCatalogue();
        $druid =
            CharacterClass::fromString('druid');

        self::assertTrue(
            $catalogue->supports($druid)
        );

        self::assertSame(
            'druid',
            $catalogue
                ->forLevel(
                    $druid,
                    5
                )['class']
        );
    }

    public function testDruidCircleSelectionBeginsAtLevelTwo(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Druid Circle',
            $definition['label']
        );

        self::assertSame(
            'Circle Grove Folio',
            $definition['folio_label']
        );

        self::assertSame(
            'druid-circle',
            $definition['choice_key']
        );

        self::assertSame(
            2,
            $definition['selection_level']
        );
    }

    public function testSixExistingMarketrealmCirclesBecomeLegalCandidates(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertCount(
            6,
            $candidates
        );

        self::assertSame(
            [
                'circle-of-eating-fresh',
                'circle-of-the-groveflame',
                'circle-of-the-deep-soil',
                'circle-of-the-compost',
                'circle-of-curdle',
                'circle-of-the-churn',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testCircleGiftsRemainExplicitlyUnimplementedInCallingSlice(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ([
            'circle-of-eating-fresh',
            'circle-of-the-groveflame',
            'circle-of-the-deep-soil',
            'circle-of-the-compost',
            'circle-of-curdle',
            'circle-of-the-churn',
        ] as $circle) {
            self::assertFalse(
                $catalogue->supports($circle)
            );

            self::assertSame(
                [],
                $catalogue->all($circle)
            );
        }
    }

    public function testCapabilityAuditSeesDruidAsSpellcastingPathSpecialist(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasSpellcastingProgression()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );
    }

    public function testDruidSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString(
            'new DruidProgression()',
            $source
        );

        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new DruidProgression()'
            )
        );
    }

    public function testArcanePantryAlreadyUsesWisdomAndFullCasterSlotsForDruid(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/'
            . 'Services/ArcanePantryPresenter.php'
        );

        self::assertStringContainsString(
            "'cleric', 'druid', 'ranger'",
            $source
        );

        self::assertStringContainsString(
            "['bard','cleric','druid','sorcerer','wizard']",
            $source
        );
    }

    public function testDruidProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new DruidProgression()
        )->forLevel(
            CharacterClass::fromString('fighter'),
            2
        );
    }

    public function testDruidSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new DruidSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
            $this->root()
            . '/'
            . $relative
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
