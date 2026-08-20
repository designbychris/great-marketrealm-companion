<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\ClericSacredReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericSacredDomainRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClericSacredReservesRegressionTest extends TestCase
{
    public function testLevelOneClericWithoutFiniteDomainResourceHasNoSacredReserve(): void
    {
        self::assertSame(
            [],
            $this->reserves(
                $this->cleric(1)
            )
        );
    }

    public function testChannelDivinityBeginsAtLevelTwoWithOneUse(): void
    {
        $reserve = $this->reserves(
            $this->cleric(2)
        )[0];

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $reserve['resource']
        );

        self::assertSame(
            1,
            $reserve['maximum']
        );

        self::assertSame(
            'short-or-long-rest',
            $reserve['refresh']
        );
    }

    public function testChannelDivinityScalesAtSixAndEighteen(): void
    {
        self::assertSame(
            2,
            $this->reserves(
                $this->cleric(6)
            )[0]['maximum']
        );

        self::assertSame(
            3,
            $this->reserves(
                $this->cleric(18)
            )[0]['maximum']
        );
    }

    public function testSpendingChannelDivinityPersistsAndShortRestRestoresIt(): void
    {
        $service =
            new ClericSacredReserveService();

        $cleric = $this->cleric(2);

        $spent = $service->spend(
            $cleric,
            ActiveClassResourceState::fresh(),
            ClericSacredReserveService::CHANNEL_DIVINITY
        );

        self::assertSame(
            1,
            $spent->expended(
                ClericSacredReserveService::CHANNEL_DIVINITY
            )
        );

        $rested = $service->shortRest(
            $cleric,
            $spent
        );

        self::assertSame(
            0,
            $rested->expended(
                ClericSacredReserveService::CHANNEL_DIVINITY
            )
        );
    }

    public function testShortRestDoesNotRestoreLongRestOnlyDomainResources(): void
    {
        $service =
            new ClericSacredReserveService();

        $cleric = $this->cleric(
            17,
            'domain-of-dairy'
        );

        $state = ActiveClassResourceState::fromArray([
            ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ClericSacredReserveService::STINKY_SALVATION => 1,
            ClericSacredReserveService::HOLY_BUTTERSTORM => 1,
        ]);

        $rested = $service->shortRest(
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

    public function testLongRestRestoresAllClericSacredReserves(): void
    {
        $cleric = $this->cleric(
            17,
            'domain-of-seasoning'
        );

        $state = ActiveClassResourceState::fromArray([
            ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ClericSacredReserveService::ZEST => 1,
            ClericSacredReserveService::PERFECT_BALANCE => 1,
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
            0,
            $rested->expended(
                ClericSacredReserveService::PERFECT_BALANCE
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testSweetnessTracksOnlyFreeSugarcloudUseBeyondSharedChannelPool(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-sweetness'
                )
            ),
            'resource'
        );

        self::assertSame(
            [
                ClericSacredReserveService::CHANNEL_DIVINITY,
                ClericSacredReserveService::SUGARCLOUD_ASCENSION,
            ],
            $keys
        );
    }

    public function testSugarcloudFreeUseRemainsSeparateFromFifthLevelSlotAlternative(): void
    {
        $reserve = $this->index(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-sweetness'
                )
            )
        )[
            ClericSacredReserveService::SUGARCLOUD_ASCENSION
        ];

        self::assertSame(
            1,
            $reserve['maximum']
        );

        self::assertStringContainsString(
            '5th-level spell slot',
            $reserve['basis']
        );
    }

    public function testGoldenArchesTracksHappyHealHourAtSeventeen(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-the-golden-arches'
                )
            ),
            'resource'
        );

        self::assertContains(
            ClericSacredReserveService::HAPPY_HEAL_HOUR,
            $keys
        );
    }

    public function testDairyTracksStinkySalvationAndHolyButterstorm(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-dairy'
                )
            ),
            'resource'
        );

        self::assertContains(
            ClericSacredReserveService::STINKY_SALVATION,
            $keys
        );

        self::assertContains(
            ClericSacredReserveService::HOLY_BUTTERSTORM,
            $keys
        );
    }

    public function testSeasoningTracksZestFromLevelOne(): void
    {
        $reserves = $this->reserves(
            $this->cleric(
                1,
                'domain-of-seasoning'
            )
        );

        self::assertCount(
            1,
            $reserves
        );

        self::assertSame(
            ClericSacredReserveService::ZEST,
            $reserves[0]['resource']
        );
    }

    public function testCultivationTracksSacredVintageAtSeventeen(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-cultivation'
                )
            ),
            'resource'
        );

        self::assertContains(
            ClericSacredReserveService::SACRED_VINTAGE,
            $keys
        );
    }

    public function testFermentTouchUsesWisdomModifierPerLongRest(): void
    {
        $cleric = $this->cleric(
            1,
            'domain-of-fermentation',
            16
        );

        $reserve = $this->reserves(
            $cleric
        )[0];

        self::assertSame(
            ClericSacredReserveService::FERMENT_TOUCH,
            $reserve['resource']
        );

        self::assertSame(
            3,
            $reserve['maximum']
        );

        self::assertStringContainsString(
            'once per creature per long rest',
            $reserve['basis']
        );
    }

    public function testFermentTouchNeverCreatesNegativeOrZeroUsablePool(): void
    {
        $reserve = $this->reserves(
            $this->cleric(
                1,
                'domain-of-fermentation',
                8
            )
        )[0];

        self::assertSame(
            1,
            $reserve['maximum']
        );
    }

    public function testMotherCultureAppearsAtSeventeen(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(
                    17,
                    'domain-of-fermentation',
                    16
                )
            ),
            'resource'
        );

        self::assertContains(
            ClericSacredReserveService::MOTHER_CULTURE,
            $keys
        );
    }

    public function testDomainChannelFeaturesDoNotCreateDuplicateCounters(): void
    {
        foreach ([
            'domain-of-sweetness',
            'domain-of-the-golden-arches',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ] as $domain) {
            $keys = array_column(
                $this->reserves(
                    $this->cleric(
                        2,
                        $domain
                    )
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

    public function testPassiveAndPerRoundFeaturesDoNotInventCounters(): void
    {
        $fermentation = array_column(
            $this->reserves(
                $this->cleric(
                    8,
                    'domain-of-fermentation',
                    16
                )
            ),
            'resource'
        );

        self::assertNotContains(
            'cleric-spiritual-brine',
            $fermentation
        );

        self::assertNotContains(
            'cleric-pickled-spirits',
            $fermentation
        );
    }

    public function testDivineInterventionDoesNotInventReserveWithoutCertifiedCadence(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->cleric(20)
            ),
            'resource'
        );

        self::assertNotContains(
            'cleric-divine-intervention',
            $keys
        );
    }

    public function testSacredDomainRegisterCarriesLiveReserveState(): void
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

        self::assertTrue(
            $register[
                'channel_divinity'
            ]['resource_tracking']
        );

        self::assertSame(
            0,
            $register[
                'channel_divinity'
            ]['remaining']
        );

        self::assertNotSame(
            [],
            $register[
                'sacred_reserves'
            ]
        );
    }

    public function testRoutesUseDedicatedDevotionNamespaceInsteadOfPaladinSacredNamespace(): void
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

    public function testControllerExposesClericSacredSpendAndRestHandlers(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'spendClericSacredReserve',
            $controller
        );

        self::assertStringContainsString(
            'restClericSacredReserves',
            $controller
        );

        self::assertStringContainsString(
            'ClericSacredReserveService',
            $controller
        );
    }

    public function testDevotionRoutesHaveDedicatedNonceFamily(): void
    {
        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'devotion/(?:spend|rest)',
            $provider
        );

        self::assertStringContainsString(
            'gmrc_character_devotion_',
            $provider
        );
    }

    public function testLedgerShowsSpendAndBothSacredRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-cleric-sacred-reserves',
            $view
        );

        self::assertStringContainsString(
            'data-cleric-sacred-spend=',
            $view
        );

        self::assertStringContainsString(
            'Take a Sacred Short Rest',
            $view
        );

        self::assertStringContainsString(
            'Take a Sacred Long Rest',
            $view
        );
    }

    public function testSacredReserveUiIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-cleric-sacred-reserves',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 620px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
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
     * @return array<int,array<string,mixed>>
     */
    private function reserves(
        Character $cleric
    ): array {
        return (
            new ClericSacredReserveService()
        )->reserves(
            $cleric,
            ActiveClassResourceState::fresh()
        );
    }

    /**
     * @param array<int,array<string,mixed>> $reserves
     * @return array<string,array<string,mixed>>
     */
    private function index(
        array $reserves
    ): array {
        $indexed = [];

        foreach ($reserves as $reserve) {
            $indexed[
                (string) $reserve['resource']
            ] = $reserve;
        }

        return $indexed;
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
                'Sacred Reserve Tester'
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
