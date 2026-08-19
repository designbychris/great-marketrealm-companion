<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Sorcerer;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\SorcererProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\SorcererSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SorcererCallingRegressionTest extends TestCase
{
    public function testSorcererUsesSpecialistProgressionDefinition(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('sorcerer'),
            2
        );

        self::assertSame(
            'sorcerer',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelOneFoundationsAreSpellcastingAndOrigin(): void
    {
        $foundations = (
            new SorcererProgression()
        )->foundations(
            CharacterClass::fromString('sorcerer')
        );

        self::assertSame(
            [
                'spellcasting',
                'sorcerous-origin',
            ],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testFontOfMagicBeginsAtLevelTwo(): void
    {
        $entry = (
            new SorcererProgression()
        )->forLevel(
            CharacterClass::fromString('sorcerer'),
            2
        );

        self::assertSame(
            'font-of-magic',
            $entry['automatic'][0]['key']
        );

        self::assertSame(
            'sorcerer-level',
            $entry['automatic'][0][
                'sorcery_point_maximum'
            ]
        );
    }

    public function testMetamagicCadenceIsTwoThenThreeThenFourOptions(): void
    {
        $progression =
            new SorcererProgression();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        foreach ([
            3 => 2,
            10 => 3,
            17 => 4,
        ] as $level => $known) {
            $entry = $progression->forLevel(
                $sorcerer,
                $level
            );

            self::assertSame(
                'metamagic',
                $entry['automatic'][0]['key']
            );

            self::assertSame(
                $known,
                $entry['automatic'][0][
                    'options_known'
                ]
            );
        }
    }

    public function testSorcerousRestorationIsLevelTwentyCapstone(): void
    {
        $entry = (
            new SorcererProgression()
        )->forLevel(
            CharacterClass::fromString('sorcerer'),
            20
        );

        self::assertSame(
            ['sorcerous-restoration'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            4,
            $entry['automatic'][0][
                'restored_points'
            ]
        );
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression =
            new SorcererProgression();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        foreach (
            [4, 8, 12, 16, 19]
            as $level
        ) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $sorcerer,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testLaterOriginGiftMilestonesAreReserved(): void
    {
        $progression =
            new SorcererProgression();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        foreach ([6, 14, 18] as $level) {
            self::assertSame(
                'path-gifts',
                $progression
                    ->forLevel(
                        $sorcerer,
                        $level
                    )['delegated'][0]['folio']
            );
        }
    }

    public function testSorcerousOriginIsLevelOnePathChoice(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Sorcerous Origin',
            $definition['label']
        );

        self::assertSame(
            'Origin Spark Folio',
            $definition['folio_label']
        );

        self::assertSame(
            'sorcerous-origin',
            $definition['choice_key']
        );

        self::assertSame(
            1,
            $definition['selection_level']
        );
    }

    public function testRepoProvidesFiveMarketrealmSorcerousOrigins(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertCount(
            5,
            $candidates
        );

        self::assertSame(
            [
                'juiced-blooded',
                'sugarspark-soul',
                'carbonation-soul',
                'soda-born',
                'dairyblooded-soul',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testSorcererUsesKnownSpellCastingModel(): void
    {
        $definition =
            new SorcererSpellcastingProgression();

        $entry = $definition->forLevel(
            CharacterClass::fromString('sorcerer'),
            2
        );

        self::assertSame(
            'known-spells',
            $entry['model']
        );

        self::assertSame(
            3,
            $entry['spells_known']
        );

        self::assertSame(
            4,
            $entry['cantrips_known']
        );

        self::assertSame(
            1,
            $entry['maximum_spell_level']
        );
    }

    public function testSorcererSpellKnowledgeCadenceReachesFifteenAndSix(): void
    {
        $definition =
            new SorcererSpellcastingProgression();

        $entry = $definition->forLevel(
            CharacterClass::fromString('sorcerer'),
            20
        );

        self::assertSame(
            15,
            $entry['spells_known']
        );

        self::assertSame(
            6,
            $entry['cantrips_known']
        );

        self::assertSame(
            9,
            $entry['maximum_spell_level']
        );
    }

    public function testCantripGrowthOccursAtFourAndTen(): void
    {
        $definition =
            new SorcererSpellcastingProgression();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $sorcerer,
                    4
                )['cantrips_learned']
        );

        self::assertSame(
            1,
            $definition
                ->forLevel(
                    $sorcerer,
                    10
                )['cantrips_learned']
        );

        self::assertSame(
            0,
            $definition
                ->forLevel(
                    $sorcerer,
                    11
                )['cantrips_learned']
        );
    }

    public function testSpellcastingCatalogueRecognisesSorcerer(): void
    {
        $catalogue =
            new SpellcastingProgressionCatalogue();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        self::assertTrue(
            $catalogue->supports(
                $sorcerer
            )
        );

        self::assertSame(
            'sorcerer',
            $catalogue
                ->forLevel(
                    $sorcerer,
                    5
                )['class']
        );
    }

    public function testCapabilityAuditSeesSorcererAsSpellcastingSpecialistPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );

        self::assertTrue(
            $profile->hasSpellcastingProgression()
        );
    }

    public function testSorcererProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new SorcererProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testSorcererSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );
    }

    public function testSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString(
            'new SorcererProgression()',
            $source
        );

        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new SorcererProgression()'
            )
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
