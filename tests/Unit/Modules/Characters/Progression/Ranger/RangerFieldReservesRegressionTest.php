<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\RangerFieldReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RangerFieldReservesRegressionTest extends TestCase
{
    public function testAislewardenDoesNotInventFiniteReserve(): void
    {
        self::assertSame(
            [],
            (
                new RangerFieldReserveService()
            )->reserves(
                $this->ranger(
                    15,
                    'aislewarden-conclave'
                ),
                ActiveClassResourceState::fresh()
            )
        );
    }

    public function testColdVaultStalkerDoesNotInventFiniteReserve(): void
    {
        self::assertSame(
            [],
            (
                new RangerFieldReserveService()
            )->reserves(
                $this->ranger(
                    15,
                    'cold-vault-stalker'
                ),
                ActiveClassResourceState::fresh()
            )
        );
    }

    public function testDeepRootGraspingRootsUsesProficiencyBonusPerLongRest(): void
    {
        $ranger = $this->ranger(
            5,
            'deep-root-warden'
        );

        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $ranger,
            ActiveClassResourceState::fresh()
        );

        self::assertCount(
            1,
            $reserves
        );

        self::assertSame(
            RangerFieldReserveService::GRASPING_ROOTS,
            $reserves[0]['resource']
        );

        self::assertSame(
            $ranger
                ->proficiencyBonus()
                ->value(),
            $reserves[0]['maximum']
        );

        self::assertSame(
            'Proficiency Bonus uses per long rest',
            $reserves[0]['basis']
        );
    }

    public function testDeepRootHeartOfRootlandsAddsOneUseAtFifteen(): void
    {
        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $this->ranger(
                15,
                'deep-root-warden'
            ),
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            [
                RangerFieldReserveService::GRASPING_ROOTS,
                RangerFieldReserveService::HEART_OF_THE_ROOTLANDS,
            ],
            array_column(
                $reserves,
                'resource'
            )
        );

        self::assertSame(
            1,
            $reserves[1]['maximum']
        );
    }

    public function testForagerOnlyTracksExplicitMiracleHarvestUse(): void
    {
        $service =
            new RangerFieldReserveService();

        self::assertSame(
            [],
            $service->reserves(
                $this->ranger(
                    14,
                    'conclave-of-the-forager'
                ),
                ActiveClassResourceState::fresh()
            )
        );

        $reserves = $service->reserves(
            $this->ranger(
                15,
                'conclave-of-the-forager'
            ),
            ActiveClassResourceState::fresh()
        );

        self::assertCount(
            1,
            $reserves
        );

        self::assertSame(
            RangerFieldReserveService::MIRACLE_HARVEST,
            $reserves[0]['resource']
        );

        self::assertSame(
            1,
            $reserves[0]['maximum']
        );
    }

    public function testSpiceTrailTracksOnlyFinalSeasoningAtFifteen(): void
    {
        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $this->ranger(
                15,
                'spice-trail-hunter'
            ),
            ActiveClassResourceState::fresh()
        );

        self::assertCount(
            1,
            $reserves
        );

        self::assertSame(
            RangerFieldReserveService::FINAL_SEASONING,
            $reserves[0]['resource']
        );

        self::assertSame(
            1,
            $reserves[0]['maximum']
        );
    }

    public function testRindrunnerAncientRindUsesWisdomModifierWithoutInventingMinimum(): void
    {
        $ranger = $this->ranger(
            15,
            'rindrunner',
            16
        );

        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $ranger,
            ActiveClassResourceState::fresh()
        );

        self::assertCount(
            1,
            $reserves
        );

        self::assertSame(
            3,
            $reserves[0]['maximum']
        );

        self::assertSame(
            'Wisdom modifier uses per long rest',
            $reserves[0]['basis']
        );
    }

    public function testSeedshotAncientSeedIsOncePerLongRest(): void
    {
        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $this->ranger(
                15,
                'seedshot-conclave'
            ),
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            RangerFieldReserveService::ANCIENT_SEED,
            $reserves[0]['resource']
        );

        self::assertSame(
            1,
            $reserves[0]['maximum']
        );
    }

    public function testExpiryHunterPutItBackUsesProficiencyBonusFromEleven(): void
    {
        $service =
            new RangerFieldReserveService();

        self::assertSame(
            [],
            $service->reserves(
                $this->ranger(
                    10,
                    'expiry-hunter'
                ),
                ActiveClassResourceState::fresh()
            )
        );

        $ranger = $this->ranger(
            11,
            'expiry-hunter'
        );

        $reserves = $service->reserves(
            $ranger,
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            RangerFieldReserveService::PUT_IT_BACK,
            $reserves[0]['resource']
        );

        self::assertSame(
            $ranger
                ->proficiencyBonus()
                ->value(),
            $reserves[0]['maximum']
        );
    }

    public function testSpendingFieldReserveReducesRemainingUse(): void
    {
        $service =
            new RangerFieldReserveService();

        $ranger = $this->ranger(
            5,
            'deep-root-warden'
        );

        $spent = $service->spend(
            $ranger,
            ActiveClassResourceState::fresh(),
            RangerFieldReserveService::GRASPING_ROOTS
        );

        $reserve = $service->reserves(
            $ranger,
            $spent
        )[0];

        self::assertSame(
            1,
            $reserve['expended']
        );

        self::assertSame(
            $reserve['maximum'] - 1,
            $reserve['remaining']
        );
    }

    public function testFieldReserveCannotOverspend(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerFieldReserveService()
        )->spend(
            $this->ranger(
                15,
                'seedshot-conclave'
            ),
            ActiveClassResourceState::fromArray([
                RangerFieldReserveService::ANCIENT_SEED => 1,
            ]),
            RangerFieldReserveService::ANCIENT_SEED
        );
    }

    public function testRangerCannotSpendReserveFromAnotherPath(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerFieldReserveService()
        )->spend(
            $this->ranger(
                15,
                'expiry-hunter'
            ),
            ActiveClassResourceState::fresh(),
            RangerFieldReserveService::ANCIENT_SEED
        );
    }

    public function testLongRestRestoresChosenPathFieldReserves(): void
    {
        $service =
            new RangerFieldReserveService();

        $ranger = $this->ranger(
            15,
            'deep-root-warden'
        );

        $state = ActiveClassResourceState::fromArray([
            RangerFieldReserveService::GRASPING_ROOTS => 2,
            RangerFieldReserveService::HEART_OF_THE_ROOTLANDS => 1,
            'spell-slot-1' => 1,
        ]);

        $rested = $service->longRest(
            $ranger,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                RangerFieldReserveService::GRASPING_ROOTS
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                RangerFieldReserveService::HEART_OF_THE_ROOTLANDS
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testFieldRegisterCarriesLiveReserveState(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(
                11,
                'expiry-hunter'
            ),
            ActiveClassResourceState::fromArray([
                RangerFieldReserveService::PUT_IT_BACK => 1,
            ])
        );

        self::assertCount(
            1,
            $register[
                'field_reserves'
            ]
        );

        self::assertSame(
            1,
            $register[
                'field_reserves'
            ][0]['expended']
        );

        self::assertSame(
            $register[
                'field_reserves'
            ][0]['maximum'] - 1,
            $register[
                'field_reserves'
            ][0]['remaining']
        );
    }

    public function testControllerRoutesAndNonceBridgeExposeFieldReserveActions(): void
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
            'spendRangerFieldReserve',
            $controller
        );

        self::assertStringContainsString(
            'restRangerFieldReserves',
            $controller
        );

        self::assertStringContainsString(
            '/field/spend',
            $routes
        );

        self::assertStringContainsString(
            '/field/rest',
            $routes
        );

        self::assertStringContainsString(
            'field/(?:spend|rest)',
            $provider
        );

        self::assertStringContainsString(
            'gmrc_character_field_',
            $provider
        );
    }

    public function testLedgerShowsFieldReserveSpendAndLongRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-ranger-field-reserves',
            $view
        );

        self::assertStringContainsString(
            'data-ranger-field-spend=',
            $view
        );

        self::assertStringContainsString(
            'data-ranger-field-rest',
            $view
        );

        self::assertStringContainsString(
            'Only abilities with an explicit finite',
            $view
        );
    }

    public function testFieldReserveUiIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-ranger-field-reserves',
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

    public function testFieldReserveRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerFieldReserveService()
        )->reserves(
            $this->character(
                'wizard',
                15,
                'seedshot-conclave'
            ),
            ActiveClassResourceState::fresh()
        );
    }

    private function ranger(
        int $level,
        string $path,
        int $wisdom = 10
    ): Character {
        return $this->character(
            'ranger',
            $level,
            $path,
            $wisdom
        );
    }

    private function character(
        string $class,
        int $level,
        string $path = '',
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
                'Field Reserve Tester'
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
                    $path
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
