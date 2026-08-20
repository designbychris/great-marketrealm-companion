<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Artificer;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationGiftLedgerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\ArtificerProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\ArtificerSpellcastingProgression;
use PHPUnit\Framework\TestCase;

final class ArtificerFinalCertificationRegressionTest extends TestCase
{
    public function testArtificerRemainsSpecialistSpellcastingPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('artificer')
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

    public function testArtificerFoundationsRemainTinkeringAndSpellcasting(): void
    {
        self::assertSame(
            [
                'magical-tinkering',
                'spellcasting',
            ],
            array_column(
                (
                    new ArtificerProgression()
                )->foundations(
                    CharacterClass::fromString(
                        'artificer'
                    )
                ),
                'key'
            )
        );
    }

    public function testCoreInventorMilestonesRemainSealed(): void
    {
        $progression =
            new ArtificerProgression();

        $class =
            CharacterClass::fromString(
                'artificer'
            );

        $expected = [
            2 => 'Infuse Item',
            3 => 'The Right Tool for the Job',
            6 => 'Tool Expertise',
            7 => 'Flash of Genius',
            10 => 'Magic Item Adept',
            11 => 'Spell-Storing Item',
            14 => 'Magic Item Savant',
            18 => 'Magic Item Master',
            20 => 'Soul of Artifice',
        ];

        foreach ($expected as $level => $label) {
            self::assertSame(
                $label,
                $progression
                    ->forLevel(
                        $class,
                        $level
                    )['automatic'][0]['label']
            );
        }
    }

    public function testPreparedIntelligenceHalfCasterThresholdsRemainSealed(): void
    {
        $progression =
            new ArtificerSpellcastingProgression();

        $class =
            CharacterClass::fromString(
                'artificer'
            );

        self::assertSame(
            'prepared-spells',
            $progression
                ->forLevel($class, 2)['model']
        );

        self::assertSame(
            'half-artificer-level + intelligence-modifier',
            $progression
                ->forLevel(
                    $class,
                    2
                )['spells_prepared_formula']
        );

        self::assertSame(
            [1, 2, 3, 4, 5],
            [
                $progression
                    ->forLevel(
                        $class,
                        2
                    )['maximum_spell_level'],
                $progression
                    ->forLevel(
                        $class,
                        5
                    )['maximum_spell_level'],
                $progression
                    ->forLevel(
                        $class,
                        9
                    )['maximum_spell_level'],
                $progression
                    ->forLevel(
                        $class,
                        13
                    )['maximum_spell_level'],
                $progression
                    ->forLevel(
                        $class,
                        17
                    )['maximum_spell_level'],
            ]
        );

        self::assertSame(
            [2, 3, 4],
            [
                $progression
                    ->forLevel(
                        $class,
                        9
                    )['cantrips_known'],
                $progression
                    ->forLevel(
                        $class,
                        10
                    )['cantrips_known'],
                $progression
                    ->forLevel(
                        $class,
                        14
                    )['cantrips_known'],
            ]
        );
    }

    public function testLevelThreeSelectionAndFourCanonicalSpecialisationsRemainSealed(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'artificer'
            )
        );

        self::assertIsArray($definition);
        self::assertSame(
            3,
            $definition['selection_level']
        );
        self::assertSame(
            'Artificer Specialisation',
            $definition['label']
        );

        self::assertSame(
            $this->specialisations(),
            array_column(
                (
                    new PathCandidateCatalogue()
                )->forClass(
                    CharacterClass::fromString(
                        'artificer'
                    )
                ),
                'key'
            )
        );
    }

    public function testSpecialisationGiftCadencesRemainSourceFaithful(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $expected = [
            'the-spice-engineer' =>
                [3, 3, 5, 9, 15],
            'the-cheesemonger' =>
                [3, 3, 5, 9, 15],
            'the-sous-sorcerer' =>
                [3, 3],
            'the-culinary-engineer' =>
                [3, 3, 5, 9, 15],
        ];

        foreach (
            $expected
            as $specialisation => $levels
        ) {
            self::assertTrue(
                $catalogue->supports(
                    $specialisation
                )
            );
            self::assertSame(
                $levels,
                array_column(
                    $catalogue->all(
                        $specialisation
                    ),
                    'level'
                )
            );
        }
    }

    public function testArtificerCatalogueRetainsSeventeenUniqueSuppliedGifts(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $keys = [];

        foreach (
            $this->specialisations()
            as $specialisation
        ) {
            foreach (
                $catalogue->all(
                    $specialisation
                )
                as $gift
            ) {
                $keys[] =
                    (string) (
                        $gift['key']
                        ?? ''
                    );
            }
        }

        self::assertCount(
            17,
            $keys
        );
        self::assertCount(
            17,
            array_unique(
                $keys
            )
        );
    }

    public function testFullProgressionStillAdvertisesItsLevelFifteenGift(): void
    {
        $register = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(
                14,
                'the-spice-engineer'
            )
        );

        self::assertSame(
            15,
            $register[
                'next_milestone'
            ]['level']
        );
        self::assertSame(
            'Final Specialisation Gift',
            $register[
                'next_milestone'
            ]['label']
        );

        $ledger = (
            new ArtificerSpecialisationGiftLedgerPresenter()
        )->present(
            $this->artificer(
                14,
                'the-spice-engineer'
            )
        );

        self::assertSame(
            15,
            $ledger['next_level']
        );
        self::assertSame(
            ['The Grand Seasoning'],
            array_column(
                $ledger['next_gifts'],
                'label'
            )
        );
    }

    public function testSousSorcererNeverInventsLaterSpecialisationGifts(): void
    {
        $register = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(
                3,
                'the-sous-sorcerer'
            )
        );

        self::assertSame(
            6,
            $register[
                'next_milestone'
            ]['level']
        );
        self::assertSame(
            'Tool Expertise',
            $register[
                'next_milestone'
            ]['label']
        );

        $ledger = (
            new ArtificerSpecialisationGiftLedgerPresenter()
        )->present(
            $this->artificer(
                15,
                'the-sous-sorcerer'
            )
        );

        self::assertTrue(
            $ledger['complete']
        );
        self::assertNull(
            $ledger['next_level']
        );
        self::assertSame(
            [],
            $ledger['next_gifts']
        );
        self::assertSame(
            2,
            $ledger['count']
        );
    }

    public function testLivingLedgerAndControllerKeepArtificerSurfacesTogether(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'ArtificerSpecialisationRegisterPresenter',
            $controller
        );
        self::assertStringContainsString(
            'ArtificerSpecialisationGiftLedgerPresenter',
            $controller
        );
        self::assertStringContainsString(
            "'artificerRegister' => \$artificerRegister",
            $controller
        );
        self::assertStringContainsString(
            "'artificerGifts' => \$artificerGifts",
            $controller
        );
        self::assertStringContainsString(
            'data-artificer-register',
            $view
        );
        self::assertStringContainsString(
            'data-artificer-specialisation-gifts',
            $view
        );
    }

    public function testFinalArtificerLedgerHardeningRemainsResponsiveAndAccessible(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-artificer-register',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-artificer-gifts__grid',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 460px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 620px)',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );
    }

    public function testPhaseThreeTwelveNowHasThirteenCertifiedSpecialistCallings(): void
    {
        $catalogue =
            new ClassCapabilityCatalogue();

        self::assertSame(
            [
                'artificer',
                'barbarian',
                'bard',
                'cleric',
                'druid',
                'fighter',
                'monk',
                'paladin',
                'ranger',
                'rogue',
                'sorcerer',
                'warlock',
                'wizard',
            ],
            array_values(
                array_unique(
                    array_map(
                        static fn (
                            ClassCapabilityProfile $profile
                        ): string =>
                            $profile
                                ->class()
                                ->value(),
                        $catalogue->specialist()
                    )
                )
            )
        );

        self::assertCount(
            13,
            $catalogue->specialist()
        );

        self::assertSame(
            [
                'grocer',
                'cleaver-saint',
            ],
            array_values(
                array_unique(
                    array_map(
                        static fn (
                            ClassCapabilityProfile $profile
                        ): string =>
                            $profile
                                ->class()
                                ->value(),
                        $catalogue->foundation()
                    )
                )
            )
        );
    }

    /** @return array<int,string> */
    private function specialisations(): array
    {
        return [
            'the-spice-engineer',
            'the-cheesemonger',
            'the-sous-sorcerer',
            'the-culinary-engineer',
        ];
    }

    private function artificer(
        int $level,
        string $specialisation = ''
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Artificer Certification Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'artificer'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $specialisation
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
