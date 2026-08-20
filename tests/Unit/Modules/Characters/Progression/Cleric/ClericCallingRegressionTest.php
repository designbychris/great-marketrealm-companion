<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\ClericProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\ClericSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClericCallingRegressionTest extends TestCase
{
    public function testClericUsesSpecialistProgressionDefinition(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('cleric'),
            2
        );

        self::assertSame(
            'cleric',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelOneFoundationsRemainSpellcastingAndDivineDomain(): void
    {
        $foundations = (
            new ClericProgression()
        )->foundations(
            CharacterClass::fromString('cleric')
        );

        self::assertSame(
            [
                'spellcasting',
                'divine-domain',
            ],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testLevelTwoIntroducesChannelDivinityAndTurnUndead(): void
    {
        $entry = (
            new ClericProgression()
        )->forLevel(
            CharacterClass::fromString('cleric'),
            2
        );

        self::assertSame(
            [
                'channel-divinity',
                'turn-undead',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testChannelDivinityImprovesAtSixAndEighteen(): void
    {
        $progression =
            new ClericProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        foreach ([6, 18] as $level) {
            self::assertContains(
                'channel-divinity-improvement',
                array_column(
                    $progression
                        ->forLevel(
                            $cleric,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testDestroyUndeadThresholdsRemainCanonical(): void
    {
        $progression =
            new ClericProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertSame(
            'CR 1/2',
            $progression
                ->forLevel(
                    $cleric,
                    5
                )['automatic'][0]['threshold']
        );

        self::assertSame(
            'CR 1',
            $progression
                ->forLevel(
                    $cleric,
                    8
                )['automatic'][0]['threshold']
        );

        self::assertSame(
            'CR 2',
            $progression
                ->forLevel(
                    $cleric,
                    11
                )['automatic'][0]['threshold']
        );

        self::assertSame(
            'CR 3',
            $progression
                ->forLevel(
                    $cleric,
                    14
                )['automatic'][0]['threshold']
        );

        self::assertSame(
            'CR 4',
            $progression
                ->forLevel(
                    $cleric,
                    17
                )['automatic'][0]['threshold']
        );
    }

    public function testDivineInterventionAppearsAtTenAndImprovesAtTwenty(): void
    {
        $progression =
            new ClericProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertSame(
            ['divine-intervention'],
            array_column(
                $progression
                    ->forLevel(
                        $cleric,
                        10
                    )['automatic'],
                'key'
            )
        );

        self::assertSame(
            ['divine-intervention-improvement'],
            array_column(
                $progression
                    ->forLevel(
                        $cleric,
                        20
                    )['automatic'],
                'key'
            )
        );
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression =
            new ClericProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $cleric,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testDomainGiftMilestonesRemainDelegatedForLaterSlice(): void
    {
        $progression =
            new ClericProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        foreach ([2, 6, 8, 17] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $cleric,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testClericUsesPreparedSpellFullCasterModel(): void
    {
        $entry = (
            new ClericSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('cleric'),
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
            'cleric-level + wisdom-modifier',
            $entry['spells_prepared_formula']
        );

        self::assertSame(
            1,
            $entry['minimum_spells_prepared']
        );
    }

    public function testClericCantripProgressionRemainsThreeFourFive(): void
    {
        $definition =
            new ClericSpellcastingProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertSame(
            3,
            $definition
                ->forLevel(
                    $cleric,
                    2
                )['cantrips_known']
        );

        self::assertSame(
            4,
            $definition
                ->forLevel(
                    $cleric,
                    4
                )['cantrips_known']
        );

        self::assertSame(
            5,
            $definition
                ->forLevel(
                    $cleric,
                    10
                )['cantrips_known']
        );
    }

    public function testCantripGainsOnlyOccurAtFourAndTenAfterLevelOne(): void
    {
        $definition =
            new ClericSpellcastingProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $cleric,
                    4
                )['cantrips_learned']
        );

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $cleric,
                    10
                )['cantrips_learned']
        );

        self::assertSame(
            0,
            $definition
                ->forLevel(
                    $cleric,
                    11
                )['cantrips_learned']
        );
    }

    public function testClericFullCastingReachesNinthCircleAtSeventeen(): void
    {
        $definition =
            new ClericSpellcastingProgression();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $cleric,
                    2
                )['maximum_spell_level']
        );

        self::assertSame(
            5,
            $definition
                ->forLevel(
                    $cleric,
                    9
                )['maximum_spell_level']
        );

        self::assertSame(
            9,
            $definition
                ->forLevel(
                    $cleric,
                    17
                )['maximum_spell_level']
        );
    }

    public function testSpellcastingCatalogueRecognisesCleric(): void
    {
        $catalogue =
            new SpellcastingProgressionCatalogue();
        $cleric =
            CharacterClass::fromString('cleric');

        self::assertTrue(
            $catalogue->supports($cleric)
        );

        self::assertSame(
            'cleric',
            $catalogue
                ->forLevel(
                    $cleric,
                    5
                )['class']
        );
    }

    public function testDivineDomainSelectionBeginsAtLevelOne(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('cleric')
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Divine Domain',
            $definition['label']
        );

        self::assertSame(
            'Sacred Domain Folio',
            $definition['folio_label']
        );

        self::assertSame(
            'cleric-domain',
            $definition['choice_key']
        );

        self::assertSame(
            1,
            $definition['selection_level']
        );
    }

    public function testSixExistingMarketrealmDomainsBecomeLegalCandidates(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('cleric')
        );

        self::assertCount(
            6,
            $candidates
        );

        self::assertSame(
            [
                'domain-of-sweetness',
                'domain-of-the-golden-arches',
                'domain-of-dairy',
                'domain-of-seasoning',
                'domain-of-cultivation',
                'domain-of-fermentation',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testDomainGiftsAreCertifiedBySacredDomainsSlice(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ([
            'domain-of-sweetness',
            'domain-of-the-golden-arches',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ] as $domain) {
            self::assertTrue(
                $catalogue->supports($domain)
            );

            self::assertSame(
                [1, 2, 6, 8, 17],
                array_column(
                    $catalogue->all($domain),
                    'level'
                )
            );
        }
    }

    public function testCapabilityAuditSeesClericAsSpellcastingPathSpecialist(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('cleric')
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

    public function testClericSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString(
            'new ClericProgression()',
            $source
        );

        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new ClericProgression()'
            )
        );
    }

    public function testArcanePantryAlreadyUsesWisdomAndFullCasterSlotsForCleric(): void
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

    public function testClericAlreadyHasAClericOnlyArcaneCantrip(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/'
            . 'Models/ArcaneAbilityCatalogue.php'
        );

        self::assertStringContainsString(
            "'sacred-brine'",
            $source
        );

        self::assertStringContainsString(
            "['cleric']",
            $source
        );
    }

    public function testClericProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new ClericProgression()
        )->forLevel(
            CharacterClass::fromString('fighter'),
            2
        );
    }

    public function testClericSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new ClericSpellcastingProgression()
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
