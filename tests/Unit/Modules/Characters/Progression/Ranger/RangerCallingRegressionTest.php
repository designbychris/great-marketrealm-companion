<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RangerProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\RangerSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RangerCallingRegressionTest extends TestCase
{
    public function testRangerUsesSpecialistProgressionDefinition(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            2
        );

        self::assertSame(
            'ranger',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testRangerLevelOneFoundationsKeepFavouredMarkAndNaturalExplorer(): void
    {
        $foundations = (
            new RangerProgression()
        )->foundations(
            CharacterClass::fromString('ranger')
        );

        self::assertSame(
            [
                'favoured-mark',
                'natural-explorer',
            ],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testLevelTwoAddsFightingStyleAndSpellcasting(): void
    {
        $entry = (
            new RangerProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            2
        );

        self::assertSame(
            [
                'fighting-style',
                'spellcasting',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testPrimevalAwarenessIsLevelThreeCallingFeature(): void
    {
        $entry = (
            new RangerProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            3
        );

        self::assertSame(
            ['primeval-awareness'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testExtraAttackArrivesAtLevelFive(): void
    {
        self::assertSame(
            ['extra-attack'],
            array_column(
                (
                    new RangerProgression()
                )->forLevel(
                    CharacterClass::fromString('ranger'),
                    5
                )['automatic'],
                'key'
            )
        );
    }

    public function testLaterRangerMilestonesRemainInCallingSpine(): void
    {
        $progression = new RangerProgression();
        $ranger = CharacterClass::fromString('ranger');

        foreach ([
            8 => 'lands-stride',
            10 => 'hide-in-plain-sight',
            14 => 'vanish',
            18 => 'feral-senses',
            20 => 'foe-slayer',
        ] as $level => $feature) {
            self::assertContains(
                $feature,
                array_column(
                    $progression
                        ->forLevel(
                            $ranger,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression = new RangerProgression();
        $ranger = CharacterClass::fromString('ranger');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $ranger,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testRangerUsesKnownSpellHalfCasterModel(): void
    {
        $entry = (
            new RangerSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            2
        );

        self::assertSame(
            'known-spells',
            $entry['model']
        );

        self::assertSame(
            2,
            $entry['spells_known']
        );

        self::assertSame(
            0,
            $entry['cantrips_known']
        );

        self::assertSame(
            1,
            $entry['maximum_spell_level']
        );
    }

    public function testRangerSpellKnowledgeReachesElevenAtTwenty(): void
    {
        $entry = (
            new RangerSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            20
        );

        self::assertSame(
            11,
            $entry['spells_known']
        );

        self::assertSame(
            5,
            $entry['maximum_spell_level']
        );

        self::assertSame(
            0,
            $entry['cantrips_known']
        );
    }

    public function testRangerSpellCirclesAdvanceAtCertifiedHalfCasterThresholds(): void
    {
        $definition = new RangerSpellcastingProgression();
        $ranger = CharacterClass::fromString('ranger');

        foreach ([
            2 => 1,
            5 => 2,
            9 => 3,
            13 => 4,
            17 => 5,
        ] as $level => $spellLevel) {
            self::assertSame(
                $spellLevel,
                $definition
                    ->forLevel(
                        $ranger,
                        $level
                    )['maximum_spell_level']
            );
        }
    }

    public function testSpellcastingCatalogueRecognisesRanger(): void
    {
        $catalogue =
            new SpellcastingProgressionCatalogue();
        $ranger =
            CharacterClass::fromString(
                'ranger'
            );

        self::assertTrue(
            $catalogue->supports(
                $ranger
            )
        );

        self::assertSame(
            'ranger',
            $catalogue
                ->forLevel(
                    $ranger,
                    5
                )['class']
        );
    }

    public function testCapabilityAuditSeesRangerAsSpellcastingSpecialist(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('ranger')
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

        self::assertFalse(
            $profile->hasCallingPathProgression()
        );
    }

    public function testRangerPathCatalogueRemainsUnregisteredWithoutSubclassCandidates(): void
    {
        $ranger =
            CharacterClass::fromString(
                'ranger'
            );

        self::assertSame(
            [],
            (new PathCandidateCatalogue())
                ->forClass($ranger)
        );

        self::assertNull(
            (new PathProgressionCatalogue())
                ->forClass($ranger)
        );
    }

    public function testRangerProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new RangerProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testRangerSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );
    }

    public function testRangerSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString(
            'new RangerProgression()',
            $source
        );

        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new RangerProgression()'
            )
        );
    }

    public function testArcanePantryAlreadyRecognisesRangerWisdomAndHalfCasterSlots(): void
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
            "['paladin','ranger']",
            $source
        );
    }

    public function testFavouredMarkAlreadyExistsAsRangerArcaneFeature(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/'
            . 'Models/ArcaneAbilityCatalogue.php'
        );

        self::assertStringContainsString(
            "'favoured-mark'",
            $source
        );

        self::assertStringContainsString(
            "['ranger']",
            $source
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
