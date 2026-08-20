<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\ClericSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\ClericProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericDomainSpellCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericDivineArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericSacredDomainRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\ClericSpellcastingProgression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClericFinalSealRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function domains(): array
    {
        return [
            'domain-of-sweetness',
            'domain-of-the-golden-arches',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ];
    }

    public function testClericRemainsSpecialistSpellcastingPathCalling(): void
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

    public function testClericFoundationsRemainSpellcastingAndDomain(): void
    {
        $foundations = (
            new ClericProgression()
        )->foundations(
            CharacterClass::fromString('cleric')
        );

        self::assertSame(
            ['spellcasting', 'divine-domain'],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testDomainSelectionRemainsLevelOne(): void
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
            $this->domains(),
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testEveryDomainKeepsCompleteOneTwoSixEightSeventeenCadence(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ($this->domains() as $domain) {
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

    public function testClericRemainsPreparedWisdomFullCaster(): void
    {
        $entry = (
            new ClericSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('cleric'),
            10
        );

        self::assertSame(
            'prepared-spells',
            $entry['model']
        );

        self::assertSame(
            'cleric-level + wisdom-modifier',
            $entry['spells_prepared_formula']
        );

        self::assertSame(
            5,
            $entry['cantrips_known']
        );

        self::assertSame(
            5,
            $entry['maximum_spell_level']
        );
    }

    public function testClericStillReachesNinthCircleAtSeventeen(): void
    {
        self::assertSame(
            9,
            (
                new ClericSpellcastingProgression()
            )->forLevel(
                CharacterClass::fromString('cleric'),
                17
            )['maximum_spell_level']
        );
    }

    public function testFiveCompleteDomainSpellTablesRemainComplete(): void
    {
        $catalogue =
            new ClericDomainSpellCatalogue();

        foreach ([
            'domain-of-sweetness',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ] as $domain) {
            self::assertSame(
                [1, 3, 5, 7, 9],
                array_column(
                    $catalogue->forDomain($domain),
                    'level'
                )
            );
        }
    }

    public function testGoldenArchesPartialSpellTableRemainsIntentional(): void
    {
        self::assertSame(
            [
                ['level' => 1, 'spells' => ['Grease']],
                [
                    'level' => 5,
                    'spells' => ['Create Food and Water'],
                ],
            ],
            (
                new ClericDomainSpellCatalogue()
            )->forDomain(
                'domain-of-the-golden-arches'
            )
        );
    }

    public function testDairyGreaseRemainsAFirstLevelSpellNotCantrip(): void
    {
        $gift = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-dairy'
        )[0];

        self::assertStringContainsString(
            'Grease is a 1st-level spell, not a cantrip',
            $gift['detail']
        );
    }

    public function testCurdledBlessingRemainsLevelTwoChannelDivinity(): void
    {
        $gift = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-dairy'
        )[1];

        self::assertSame(
            2,
            $gift['level']
        );

        self::assertSame(
            'Channel Divinity: Curdled Blessing',
            $gift['label']
        );
    }

    public function testChannelDivinityRemainsOneSharedPool(): void
    {
        $service =
            new ClericSacredReserveService();

        foreach ($this->domains() as $domain) {
            $keys = array_column(
                $service->reserves(
                    $this->cleric(
                        2,
                        $domain
                    ),
                    ActiveClassResourceState::fresh()
                ),
                'resource'
            );

            self::assertSame(
                1,
                count(
                    array_filter(
                        $keys,
                        static fn (
                            string $key
                        ): bool =>
                            $key
                            === ClericSacredReserveService::CHANNEL_DIVINITY
                    )
                )
            );
        }
    }

    public function testChannelDivinityScalesOneTwoThree(): void
    {
        $service =
            new ClericSacredReserveService();

        self::assertSame(
            1,
            $service->reserves(
                $this->cleric(2),
                ActiveClassResourceState::fresh()
            )[0]['maximum']
        );

        self::assertSame(
            2,
            $service->reserves(
                $this->cleric(6),
                ActiveClassResourceState::fresh()
            )[0]['maximum']
        );

        self::assertSame(
            3,
            $service->reserves(
                $this->cleric(18),
                ActiveClassResourceState::fresh()
            )[0]['maximum']
        );
    }

    public function testShortRestOnlyRestoresShortRestSacredResources(): void
    {
        $cleric = $this->cleric(
            17,
            'domain-of-dairy'
        );

        $state = ActiveClassResourceState::fromArray([
            ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ClericSacredReserveService::STINKY_SALVATION => 1,
            ClericSacredReserveService::HOLY_BUTTERSTORM => 1,
        ]);

        $rested = (
            new ClericSacredReserveService()
        )->shortRest(
            $cleric,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                ClericSacredReserveService::CHANNEL_DIVINITY
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                ClericSacredReserveService::STINKY_SALVATION
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                ClericSacredReserveService::HOLY_BUTTERSTORM
            )
        );
    }

    public function testLongRestKeepsSpellSlotOwnershipSeparate(): void
    {
        $cleric = $this->cleric(
            17,
            'domain-of-seasoning'
        );

        $state = ActiveClassResourceState::fromArray([
            ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ClericSacredReserveService::ZEST => 1,
            'spell-slot-1' => 1,
        ]);

        $rested = (
            new ClericSacredReserveService()
        )->longRest(
            $cleric,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                ClericSacredReserveService::CHANNEL_DIVINITY
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                ClericSacredReserveService::ZEST
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testSharedSpellSlotServiceCompletesLongRestCycle(): void
    {
        $cleric = $this->cleric(
            5,
            'domain-of-sweetness'
        );

        $rested = (
            new SharedSpellSlotReserveService()
        )->longRest(
            $cleric,
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        );

        self::assertSame(
            0,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testFermentTouchRemainsWisdomBasedWithMinimumOneUse(): void
    {
        $service =
            new ClericSacredReserveService();

        $highWisdom = $service->reserves(
            $this->cleric(
                1,
                'domain-of-fermentation',
                16
            ),
            ActiveClassResourceState::fresh()
        )[0];

        $lowWisdom = $service->reserves(
            $this->cleric(
                1,
                'domain-of-fermentation',
                8
            ),
            ActiveClassResourceState::fresh()
        )[0];

        self::assertSame(
            3,
            $highWisdom['maximum']
        );

        self::assertSame(
            1,
            $lowWisdom['maximum']
        );
    }

    public function testSweetSanctuaryStillUsesLevelPlusWisdom(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    5,
                    'domain-of-sweetness',
                    16
                )
            )['arts'],
            'sweet-sanctuary'
        );

        self::assertSame(
            '8',
            $art['static']['Temporary HP']
        );
    }

    public function testDivineStrikeScalingRemainsOneD8ThenTwoD8(): void
    {
        $presenter =
            new ClericDivineArtsPresenter();

        foreach ([
            'domain-of-sweetness' => 'sticky-smite',
            'domain-of-the-golden-arches' => 'golden-fry-strike',
            'domain-of-dairy' => 'cultured-smite',
            'domain-of-seasoning' => 'seasoned-divine-strike',
        ] as $domain => $key) {
            $early = $this->art(
                $presenter->present(
                    $this->cleric(
                        8,
                        $domain
                    )
                )['arts'],
                $key
            );

            $late = $this->art(
                $presenter->present(
                    $this->cleric(
                        14,
                        $domain
                    )
                )['arts'],
                $key
            );

            self::assertSame(
                '1d8',
                $early['rolls'][0]['formula']
            );

            self::assertSame(
                '2d8',
                $late['rolls'][0]['formula']
            );
        }
    }

    public function testHolyButterstormRemainsSplitRadiantAndFire(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    17,
                    'domain-of-dairy'
                )
            )['arts'],
            'holy-butterstorm'
        );

        self::assertSame(
            ['6d8', '2d8'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );

        self::assertSame(
            ['radiant', 'fire'],
            array_column(
                $art['rolls'],
                'damage_type'
            )
        );

        self::assertSame(
            ClericSacredReserveService::HOLY_BUTTERSTORM,
            $art['resource']
        );
    }

    public function testHolyButterstormButtonRemainsProminent(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'gmrc-button--holy-butterstorm',
            $view
        );

        self::assertStringContainsString(
            'UNLEASH HOLY BUTTERSTORM',
            $this->source(
                'app/Modules/Characters/Progression/Cleric/'
                . 'Services/ClericDivineArtsPresenter.php'
            )
        );
    }

    public function testFermentTouchKeepsThreeDistinctBranches(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    17,
                    'domain-of-fermentation',
                    16
                )
            )['arts'],
            'ferment-touch'
        );

        self::assertSame(
            [
                'ferment-touch-heal',
                'ferment-touch-preserve',
                'ferment-touch-sour',
            ],
            array_column(
                $art['choices'],
                'key'
            )
        );

        self::assertSame(
            '4d8',
            $art['choices'][2]['formula']
        );
    }

    public function testFunkOfTheDivineKeepsTwoD10PlusClericLevel(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    7,
                    'domain-of-fermentation'
                )
            )['arts'],
            'funk-of-the-divine'
        );

        self::assertSame(
            '2d10',
            $art['choices'][0]['formula']
        );

        self::assertSame(
            7,
            $art['choices'][0]['modifier']
        );

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $art['resource']
        );
    }

    public function testMotherCultureKeepsHealingAndDamageSeparate(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    17,
                    'domain-of-fermentation'
                )
            )['arts'],
            'mother-culture'
        );

        self::assertSame(
            '2d6',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            ['4d6', '4d6'],
            array_column(
                $art['choices'],
                'formula'
            )
        );
    }

    public function testDivineInterventionStillDoesNotInventReserve(): void
    {
        $keys = array_column(
            (
                new ClericSacredReserveService()
            )->reserves(
                $this->cleric(20),
                ActiveClassResourceState::fresh()
            ),
            'resource'
        );

        self::assertNotContains(
            'cleric-divine-intervention',
            $keys
        );
    }

    public function testSacredRegisterStillCarriesLiveChannelDivinityState(): void
    {
        $register = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(
                2,
                'domain-of-sweetness'
            ),
            ActiveClassResourceState::fromArray([
                ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ])
        );

        self::assertSame(
            0,
            $register[
                'channel_divinity'
            ]['remaining']
        );

        self::assertTrue(
            $register[
                'channel_divinity'
            ]['resource_tracking']
        );
    }

    public function testDevotionRoutesRemainSeparateFromPaladinSacredRoutes(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/characters/{id}/devotion/spend',
            $routes
        );

        self::assertStringContainsString(
            '/characters/{id}/devotion/rest',
            $routes
        );

        self::assertStringContainsString(
            '/characters/{id}/sacred/spend',
            $routes
        );
    }

    public function testDivineArtsStillUseSharedGuildDiceworksContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-cleric-divine-arts',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-modifier=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-kind=',
            $view
        );

        self::assertStringNotContainsString(
            'data-cleric-diceworks',
            $view
        );
    }

    public function testFiniteArtsStillReuseDevotionSpendRoute(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            ". '/devotion/spend'",
            $view
        );

        self::assertStringContainsString(
            'data-cleric-divine-use=',
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_devotion_',
            $view
        );
    }

    public function testFinalSealHardensVeryNarrowScreensAndLongLabels(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.11E — The Cleric's Final Seal",
            $css
        );

        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 380px)',
            $css
        );

        self::assertStringContainsString(
            'min-height: 2.75rem',
            $css
        );
    }

    public function testFinalSealPreservesReducedMotionAndForcedColours(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
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
            'ButtonText',
            $css
        );
    }

    public function testForeignCallingCannotUseClericSacredReserveService(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new ClericSacredReserveService()
        )->reserves(
            $this->character(
                'wizard',
                5,
                ''
            ),
            ActiveClassResourceState::fresh()
        );
    }

    /**
     * @param array<int,array<string,mixed>> $arts
     * @return array<string,mixed>
     */
    private function art(
        array $arts,
        string $key
    ): array {
        foreach ($arts as $art) {
            if (
                ($art['key'] ?? '')
                === $key
            ) {
                return $art;
            }
        }

        self::fail(
            'Expected Cleric Divine Art not found: '
            . $key
        );
    }

    private function cleric(
        int $level,
        string $domain = '',
        int $wisdom = 10
    ): Character {
        return $this->character(
            'cleric',
            $level,
            $domain,
            $wisdom
        );
    }

    private function character(
        string $class,
        int $level,
        string $domain,
        int $wisdom = 10
    ): Character {
        $scores = AbilityScores::average()
            ->withWisdom(
                AbilityScore::fromInt(
                    $wisdom
                )
            );

        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Cleric Final Seal'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            $scores,
            callingPath:
                CallingPath::fromString(
                    $domain
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
