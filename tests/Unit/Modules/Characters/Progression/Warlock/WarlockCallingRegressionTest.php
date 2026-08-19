<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WarlockProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WarlockCallingRegressionTest extends TestCase
{
    public function testWarlockUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('warlock'),
                2
            );

        self::assertSame('warlock', $entry['class']);
        self::assertSame('reference', $entry['catalogue_status']);
    }

    public function testLevelOneFoundationsArePactMagicAndPatron(): void
    {
        $foundations = (new WarlockProgression())
            ->foundations(
                CharacterClass::fromString('warlock')
            );

        self::assertSame(
            [
                'pact-magic',
                'otherworldly-patron',
            ],
            array_column($foundations, 'key')
        );
    }

    public function testLevelTwoBeginsWithTwoInvocations(): void
    {
        $entry = (new WarlockProgression())
            ->forLevel(
                CharacterClass::fromString('warlock'),
                2
            );

        self::assertSame(
            'eldritch-invocations',
            $entry['automatic'][0]['key']
        );

        self::assertSame(
            2,
            $entry['automatic'][0]['known']
        );
    }

    public function testPactBoonArrivesAtLevelThree(): void
    {
        $entry = (new WarlockProgression())
            ->forLevel(
                CharacterClass::fromString('warlock'),
                3
            );

        self::assertSame(
            'pact-boon',
            $entry['automatic'][0]['key']
        );
    }

    public function testPactMagicSlotLevelCadenceIsRecorded(): void
    {
        $progression = new WarlockProgression();
        $warlock = CharacterClass::fromString('warlock');

        $expected = [
            3 => [2, 2],
            5 => [3, 2],
            7 => [4, 2],
            9 => [5, 2],
            11 => [5, 3],
            17 => [5, 4],
        ];

        foreach (
            $expected
            as $level => [$slotLevel, $slots]
        ) {
            $pact = $this->automatic(
                $progression->forLevel(
                    $warlock,
                    $level
                ),
                'pact-magic'
            );

            self::assertSame(
                $slotLevel,
                $pact['slot_level']
            );

            self::assertSame(
                $slots,
                $pact['slots']
            );
        }
    }

    public function testInvocationKnownCadenceIsRecorded(): void
    {
        $progression = new WarlockProgression();
        $warlock = CharacterClass::fromString('warlock');

        $expected = [
            2 => 2,
            5 => 3,
            7 => 4,
            9 => 5,
            12 => 6,
            15 => 7,
            18 => 8,
        ];

        foreach ($expected as $level => $known) {
            self::assertSame(
                $known,
                $this->automatic(
                    $progression->forLevel(
                        $warlock,
                        $level
                    ),
                    'eldritch-invocations'
                )['known']
            );
        }
    }

    public function testMysticArcanumCadenceIsRecorded(): void
    {
        $progression = new WarlockProgression();
        $warlock = CharacterClass::fromString('warlock');

        $expected = [
            11 => 6,
            13 => 7,
            15 => 8,
            17 => 9,
        ];

        foreach (
            $expected
            as $level => $spellLevel
        ) {
            self::assertSame(
                $spellLevel,
                $this->automatic(
                    $progression->forLevel(
                        $warlock,
                        $level
                    ),
                    'mystic-arcanum-'
                    . $spellLevel
                )['spell_level']
            );
        }
    }

    public function testEldritchMasterIsLevelTwentyCapstone(): void
    {
        $entry = (new WarlockProgression())
            ->forLevel(
                CharacterClass::fromString('warlock'),
                20
            );

        self::assertSame(
            ['eldritch-master'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression = new WarlockProgression();
        $warlock = CharacterClass::fromString('warlock');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $warlock,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testLaterPatronGiftMilestonesAreReservedForPatronPhase(): void
    {
        $progression = new WarlockProgression();
        $warlock = CharacterClass::fromString('warlock');

        foreach ([6, 10, 14] as $level) {
            self::assertSame(
                'path-gifts',
                $progression
                    ->forLevel(
                        $warlock,
                        $level
                    )['delegated'][0]['folio']
            );
        }
    }

    public function testPatronPathIsAFirstLevelCallingChoice(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
        );

        self::assertIsArray($definition);
        self::assertSame(
            'Otherworldly Patron',
            $definition['label']
        );
        self::assertSame(
            'Patron Contract Folio',
            $definition['folio_label']
        );
        self::assertSame(
            'otherworldly-patron',
            $definition['choice_key']
        );
        self::assertSame(
            1,
            $definition['selection_level']
        );
    }

    public function testRepoProvidesFourMarketrealmWarlockPatrons(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
        );

        self::assertCount(4, $candidates);

        self::assertSame(
            [
                'pact-of-the-mascot',
                'the-forgotten-freezer',
                'the-spoilfather',
                'the-sugar-fiend',
            ],
            array_column($candidates, 'key')
        );
    }

    public function testCapabilityAuditNowSeesWarlockAsSpecialistPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
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

        /*
         * Pact Magic is deliberately not claimed by the Wizard-style
         * SpellcastingProgressionCatalogue in III.12.7.
         */
        self::assertFalse(
            $profile->hasSpellcastingProgression()
        );
    }

    public function testWarlockProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new WarlockProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testWarlockProgressionRejectsInvalidAdvancementLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new WarlockProgression())
            ->forLevel(
                CharacterClass::fromString('warlock'),
                1
            );
    }

    public function testSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString(
            'new WarlockProgression()',
            $source
        );

        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new WarlockProgression()'
            )
        );
    }

    private function automatic(
        array $entry,
        string $key
    ): array {
        foreach ($entry['automatic'] as $feature) {
            if (($feature['key'] ?? '') === $key) {
                return $feature;
            }
        }

        self::fail(
            'Expected Warlock automatic feature was not found.'
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
