<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Bard;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\BardProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\BardSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BardCallingRegressionTest extends TestCase
{
    public function testBardUsesSpecialistProgressionDefinition(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('bard'),
            2
        );

        self::assertSame(
            'bard',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelOneFoundationsRemainSpellcastingAndInspiration(): void
    {
        $foundations = (
            new BardProgression()
        )->foundations(
            CharacterClass::fromString('bard')
        );

        self::assertSame(
            [
                'spellcasting',
                'bardic-inspiration',
            ],
            array_column(
                $foundations,
                'key'
            )
        );

        self::assertSame(
            'd6',
            $foundations[1]['die']
        );
    }

    public function testLevelTwoAddsJackOfAllTradesAndSongOfRest(): void
    {
        $entry = (
            new BardProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            2
        );

        self::assertSame(
            [
                'jack-of-all-trades',
                'song-of-rest',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            'd6',
            $entry['automatic'][1]['die']
        );
    }

    public function testLevelThreeAddsExpertiseAndDelegatesCollegeGift(): void
    {
        $entry = (
            new BardProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            3
        );

        self::assertSame(
            ['expertise'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            ['college-gift'],
            array_column(
                $entry['delegated'],
                'key'
            )
        );
    }

    public function testBardicInspirationDieProgressesD6D8D10D12(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        self::assertSame(
            'd8',
            $this->automatic(
                $progression,
                $bard,
                5,
                'bardic-inspiration-improvement'
            )['die']
        );

        self::assertSame(
            'd10',
            $this->automatic(
                $progression,
                $bard,
                10,
                'bardic-inspiration-improvement'
            )['die']
        );

        self::assertSame(
            'd12',
            $this->automatic(
                $progression,
                $bard,
                15,
                'bardic-inspiration-improvement'
            )['die']
        );
    }

    public function testFontOfInspirationBeginsAtFive(): void
    {
        $entry = (
            new BardProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            5
        );

        self::assertContains(
            'font-of-inspiration',
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testCountercharmIsLevelSixWithoutPrematureSongOfRestImprovement(): void
    {
        $entry = (
            new BardProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            6
        );

        self::assertSame(
            ['countercharm'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testSongOfRestImprovesAtNineThirteenSeventeen(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        self::assertSame(
            'd8',
            $this->automatic(
                $progression,
                $bard,
                9,
                'song-of-rest-improvement'
            )['die']
        );

        self::assertSame(
            'd10',
            $this->automatic(
                $progression,
                $bard,
                13,
                'song-of-rest-improvement'
            )['die']
        );

        self::assertSame(
            'd12',
            $this->automatic(
                $progression,
                $bard,
                17,
                'song-of-rest-improvement'
            )['die']
        );
    }

    public function testExpertiseAppearsAtThreeAndTen(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        self::assertSame(
            2,
            $this->automatic(
                $progression,
                $bard,
                3,
                'expertise'
            )['choices']
        );

        self::assertSame(
            2,
            $this->automatic(
                $progression,
                $bard,
                10,
                'expertise'
            )['choices']
        );
    }

    public function testMagicalSecretsAppearAtTenFourteenEighteen(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        foreach ([10, 14, 18] as $level) {
            self::assertSame(
                2,
                $this->automatic(
                    $progression,
                    $bard,
                    $level,
                    'magical-secrets'
                )['choices']
            );
        }
    }

    public function testSuperiorInspirationRemainsLevelTwentyCapstone(): void
    {
        self::assertSame(
            ['superior-inspiration'],
            array_column(
                (
                    new BardProgression()
                )->forLevel(
                    CharacterClass::fromString('bard'),
                    20
                )['automatic'],
                'key'
            )
        );
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $bard,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testCollegeGiftMilestonesRemainDelegatedForLaterSlice(): void
    {
        $progression =
            new BardProgression();
        $bard =
            CharacterClass::fromString('bard');

        foreach ([3, 6, 14] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $bard,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testBardUsesKnownSpellFullCasterModel(): void
    {
        $entry = (
            new BardSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            2
        );

        self::assertSame(
            'known-spells',
            $entry['model']
        );

        self::assertSame(
            5,
            $entry['spells_known']
        );

        self::assertSame(
            1,
            $entry['spells_learned']
        );

        self::assertSame(
            2,
            $entry['cantrips_known']
        );
    }

    public function testBardCantripProgressionRemainsTwoThreeFour(): void
    {
        $definition =
            new BardSpellcastingProgression();
        $bard =
            CharacterClass::fromString('bard');

        self::assertSame(
            2,
            $definition
                ->forLevel(
                    $bard,
                    2
                )['cantrips_known']
        );

        self::assertSame(
            3,
            $definition
                ->forLevel(
                    $bard,
                    4
                )['cantrips_known']
        );

        self::assertSame(
            4,
            $definition
                ->forLevel(
                    $bard,
                    10
                )['cantrips_known']
        );
    }

    public function testMagicalSecretsLevelsAreReflectedInSpellsKnownTotals(): void
    {
        $definition =
            new BardSpellcastingProgression();
        $bard =
            CharacterClass::fromString('bard');

        self::assertSame(
            14,
            $definition
                ->forLevel(
                    $bard,
                    10
                )['spells_known']
        );

        self::assertSame(
            18,
            $definition
                ->forLevel(
                    $bard,
                    14
                )['spells_known']
        );

        self::assertSame(
            22,
            $definition
                ->forLevel(
                    $bard,
                    18
                )['spells_known']
        );
    }

    public function testBardFullCastingReachesNinthCircleAtSeventeen(): void
    {
        self::assertSame(
            9,
            (
                new BardSpellcastingProgression()
            )->forLevel(
                CharacterClass::fromString('bard'),
                17
            )['maximum_spell_level']
        );
    }

    public function testSpellcastingCatalogueRecognisesBard(): void
    {
        $catalogue =
            new SpellcastingProgressionCatalogue();
        $bard =
            CharacterClass::fromString('bard');

        self::assertTrue(
            $catalogue->supports($bard)
        );

        self::assertSame(
            'bard',
            $catalogue
                ->forLevel(
                    $bard,
                    5
                )['class']
        );
    }

    public function testBardCollegeSelectionBeginsAtLevelThree(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Bard College',
            $definition['label']
        );

        self::assertSame(
            'College Performance Folio',
            $definition['folio_label']
        );

        self::assertSame(
            'bard-college',
            $definition['choice_key']
        );

        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testSevenExistingMarketrealmCollegesBecomeLegalCandidates(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
        );

        self::assertCount(
            7,
            $candidates
        );

        self::assertSame(
            [
                'college-of-the-seasoned-song',
                'college-of-nostalgia',
                'college-of-preservation',
                'charcutaire',
                'college-of-culinary-crescendo',
                'college-of-confection',
                'college-of-churned-verse',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testCollegeGiftsRemainUnimplementedUntilCollegeSlice(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ([
            'college-of-the-seasoned-song',
            'college-of-nostalgia',
            'college-of-preservation',
            'charcutaire',
            'college-of-culinary-crescendo',
            'college-of-confection',
            'college-of-churned-verse',
        ] as $college) {
            self::assertFalse(
                $catalogue->supports($college)
            );

            self::assertSame(
                [],
                $catalogue->all($college)
            );
        }
    }

    public function testCapabilityAuditSeesBardAsSpellcastingPathSpecialist(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
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

    public function testArcanePantryAlreadyUsesCharismaAndFullCasterSlotsForBard(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/'
            . 'Services/ArcanePantryPresenter.php'
        );

        self::assertStringContainsString(
            "'bard', 'paladin', 'sorcerer', 'warlock'",
            $source
        );

        self::assertStringContainsString(
            "['bard','cleric','druid','sorcerer','wizard']",
            $source
        );
    }

    public function testBardAlreadyHasAClassSpecificArcaneCantrip(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/'
            . 'Models/ArcaneAbilityCatalogue.php'
        );

        self::assertStringContainsString(
            "'cutting-remark'",
            $source
        );

        self::assertStringContainsString(
            "['bard']",
            $source
        );
    }

    public function testBardProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BardProgression()
        )->forLevel(
            CharacterClass::fromString('fighter'),
            2
        );
    }

    public function testBardSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BardSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function automatic(
        BardProgression $progression,
        CharacterClass $bard,
        int $level,
        string $key
    ): array {
        foreach (
            $progression
                ->forLevel(
                    $bard,
                    $level
                )['automatic']
            as $feature
        ) {
            if (
                ($feature['key'] ?? '')
                === $key
            ) {
                return $feature;
            }
        }

        self::fail(
            'Expected Bard feature not found: '
            . $key
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
