<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleGroveRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DruidPrimalReservesRegressionTest extends TestCase
{
    public function testLevelOneDruidHasNoPrimalReserveYet(): void
    {
        self::assertSame(
            [],
            $this->reserves(
                $this->druid(1)
            )
        );
    }

    public function testWildShapeBeginsWithTwoUsesAtLevelTwo(): void
    {
        $reserve = $this->reserves(
            $this->druid(2)
        )[0];

        self::assertSame(
            DruidPrimalReserveService::WILD_SHAPE,
            $reserve['resource']
        );

        self::assertSame(
            2,
            $reserve['maximum']
        );

        self::assertSame(
            2,
            $reserve['remaining']
        );

        self::assertSame(
            'short-or-long-rest',
            $reserve['refresh']
        );
    }

    public function testSpendingWildShapePersistsAndShortRestRestoresIt(): void
    {
        $service =
            new DruidPrimalReserveService();

        $druid = $this->druid(2);

        $spent = $service->spend(
            $druid,
            ActiveClassResourceState::fresh(),
            DruidPrimalReserveService::WILD_SHAPE
        );

        self::assertSame(
            1,
            $spent->expended(
                DruidPrimalReserveService::WILD_SHAPE
            )
        );

        $rested = $service->shortRest(
            $druid,
            $spent
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::WILD_SHAPE
            )
        );
    }

    public function testArchdruidWildShapeIsUnlimited(): void
    {
        $druid = $this->druid(20);

        $reserve = $this->reserves(
            $druid
        )[0];

        self::assertTrue(
            $reserve['unlimited']
        );

        self::assertNull(
            $reserve['maximum']
        );

        $state = ActiveClassResourceState::fresh();

        self::assertSame(
            $state->toArray(),
            (
                new DruidPrimalReserveService()
            )->spend(
                $druid,
                $state,
                DruidPrimalReserveService::WILD_SHAPE
            )->toArray()
        );
    }

    public function testEatingFreshTracksOnlyExplicitFiniteCircleUses(): void
    {
        $early = $this->reserves(
            $this->druid(
                2,
                'circle-of-eating-fresh'
            )
        );

        self::assertSame(
            [
                DruidPrimalReserveService::WILD_SHAPE,
                DruidPrimalReserveService::CRISP_AURA_EXPANSION,
            ],
            array_column(
                $early,
                'resource'
            )
        );

        $late = $this->reserves(
            $this->druid(
                14,
                'circle-of-eating-fresh'
            )
        );

        self::assertContains(
            DruidPrimalReserveService::PRESERVATIVE_PURGE,
            array_column(
                $late,
                'resource'
            )
        );
    }

    public function testGroveflameSeparatesShortAndLongRestUses(): void
    {
        $reserves = $this->reserves(
            $this->druid(
                14,
                'circle-of-the-groveflame'
            )
        );

        $byResource = $this->index(
            $reserves
        );

        self::assertSame(
            'short-or-long-rest',
            $byResource[
                DruidPrimalReserveService::SPICE_BASILISK_BREATH
            ]['refresh']
        );

        self::assertSame(
            'long-rest',
            $byResource[
                DruidPrimalReserveService::SCORCHING_BLOOM
            ]['refresh']
        );

        self::assertSame(
            'short-or-long-rest',
            $byResource[
                DruidPrimalReserveService::PUNGENT_FLAME
            ]['refresh']
        );
    }

    public function testShortRestDoesNotRestoreLongRestOnlyGroveflameBloom(): void
    {
        $service =
            new DruidPrimalReserveService();

        $druid = $this->druid(
            14,
            'circle-of-the-groveflame'
        );

        $state = ActiveClassResourceState::fromArray([
            DruidPrimalReserveService::SPICE_BASILISK_BREATH => 1,
            DruidPrimalReserveService::SCORCHING_BLOOM => 1,
            DruidPrimalReserveService::PUNGENT_FLAME => 1,
        ]);

        $rested = $service->shortRest(
            $druid,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::SPICE_BASILISK_BREATH
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                DruidPrimalReserveService::SCORCHING_BLOOM
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::PUNGENT_FLAME
            )
        );
    }

    public function testDeepSoilLivingEarthquakeIsOncePerLongRest(): void
    {
        $byResource = $this->index(
            $this->reserves(
                $this->druid(
                    14,
                    'circle-of-the-deep-soil'
                )
            )
        );

        $reserve = $byResource[
            DruidPrimalReserveService::LIVING_EARTHQUAKE
        ];

        self::assertSame(
            1,
            $reserve['maximum']
        );

        self::assertSame(
            'long-rest',
            $reserve['refresh']
        );
    }

    public function testCompostSurgeUsesProficiencyBonusAndMulchbornUsesShortRest(): void
    {
        $druid = $this->druid(
            6,
            'circle-of-the-compost'
        );

        $byResource = $this->index(
            $this->reserves($druid)
        );

        self::assertSame(
            $druid
                ->proficiencyBonus()
                ->value(),
            $byResource[
                DruidPrimalReserveService::COMPOST_SURGE
            ]['maximum']
        );

        self::assertSame(
            'long-rest',
            $byResource[
                DruidPrimalReserveService::COMPOST_SURGE
            ]['refresh']
        );

        self::assertSame(
            'short-or-long-rest',
            $byResource[
                DruidPrimalReserveService::MULCHBORN
            ]['refresh']
        );
    }

    public function testCompostLevelTenTracksBloomAndBothSlotFreeSpellsSeparately(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->druid(
                    10,
                    'circle-of-the-compost'
                )
            ),
            'resource'
        );

        self::assertContains(
            DruidPrimalReserveService::BLOOM_OF_DECAY,
            $keys
        );

        self::assertContains(
            DruidPrimalReserveService::BLIGHT,
            $keys
        );

        self::assertContains(
            DruidPrimalReserveService::INSECT_PLAGUE,
            $keys
        );
    }

    public function testCompostAvatarDoesNotInventSeparateCounterBecauseItSpendsWildShape(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->druid(
                    14,
                    'circle-of-the-compost'
                )
            ),
            'resource'
        );

        self::assertNotContains(
            'druid-avatar-of-the-rotten-grove',
            $keys
        );

        self::assertContains(
            DruidPrimalReserveService::WILD_SHAPE,
            $keys
        );
    }

    public function testCurdleTracksAnimateSpoilButDoesNotInventBacteriaBloomUses(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->druid(
                    14,
                    'circle-of-curdle'
                )
            ),
            'resource'
        );

        self::assertContains(
            DruidPrimalReserveService::ANIMATE_SPOIL,
            $keys
        );

        self::assertNotContains(
            'druid-bacteria-bloom',
            $keys
        );
    }

    public function testChurnKeepsFrozenCurdFreeUseSeparateFromWildShape(): void
    {
        $keys = array_column(
            $this->reserves(
                $this->druid(
                    2,
                    'circle-of-the-churn'
                )
            ),
            'resource'
        );

        self::assertSame(
            [
                DruidPrimalReserveService::WILD_SHAPE,
                DruidPrimalReserveService::FROZEN_CURD,
            ],
            $keys
        );
    }

    public function testChurnGlacialGrowthUsesProficiencyBonus(): void
    {
        $druid = $this->druid(
            10,
            'circle-of-the-churn'
        );

        $byResource = $this->index(
            $this->reserves($druid)
        );

        self::assertSame(
            $druid
                ->proficiencyBonus()
                ->value(),
            $byResource[
                DruidPrimalReserveService::GLACIAL_GROWTH
            ]['maximum']
        );
    }

    public function testLongRestRestoresAllDruidPrimalReservesButNotSpellSlotsInsideService(): void
    {
        $druid = $this->druid(
            10,
            'circle-of-the-churn'
        );

        $state = ActiveClassResourceState::fromArray([
            DruidPrimalReserveService::WILD_SHAPE => 1,
            DruidPrimalReserveService::FROZEN_CURD => 1,
            DruidPrimalReserveService::GLACIAL_GROWTH => 1,
            'spell-slot-1' => 1,
        ]);

        $rested = (
            new DruidPrimalReserveService()
        )->longRest(
            $druid,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::WILD_SHAPE
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::FROZEN_CURD
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testGroveRegisterCarriesLivePrimalReserveState(): void
    {
        $register = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-churn'
            ),
            ActiveClassResourceState::fromArray([
                DruidPrimalReserveService::WILD_SHAPE => 1,
            ])
        );

        self::assertCount(
            2,
            $register[
                'primal_reserves'
            ]
        );

        self::assertSame(
            1,
            $register[
                'primal_reserves'
            ][0]['remaining']
        );
    }

    public function testRoutesControllerAndNonceExposePrimalActions(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'spendDruidPrimalReserve',
            $controller
        );

        self::assertStringContainsString(
            'restDruidPrimalReserves',
            $controller
        );

        self::assertStringContainsString(
            '/primal/spend',
            $routes
        );

        self::assertStringContainsString(
            '/primal/rest',
            $routes
        );

        self::assertStringContainsString(
            'primal/(?:spend|rest)',
            $provider
        );
    }

    public function testLedgerShowsPrimalSpendAndBothRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-druid-primal-reserves',
            $view
        );

        self::assertStringContainsString(
            'data-druid-primal-spend=',
            $view
        );

        self::assertStringContainsString(
            'data-druid-primal-rest=',
            $view
        );

        self::assertStringContainsString(
            'Take a Primal Short Rest',
            $view
        );

        self::assertStringContainsString(
            'Take a Primal Long Rest',
            $view
        );
    }

    public function testPrimalReserveUiIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-druid-primal-reserves',
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

    public function testForeignCallingCannotUsePrimalReserveService(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new DruidPrimalReserveService()
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
        Character $druid
    ): array {
        return (
            new DruidPrimalReserveService()
        )->reserves(
            $druid,
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

    private function druid(
        int $level,
        string $circle = ''
    ): Character {
        return $this->character(
            'druid',
            $level,
            $circle
        );
    }

    private function character(
        string $class,
        int $level,
        string $circle
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Primal Reserve Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString($circle)
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
