<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\BarbarianRageReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BarbarianRageReservesRegressionTest extends TestCase
{
    public function testFreshConditionStateIsNotRaging(): void
    {
        $state = ActiveClassConditionState::fresh();

        self::assertFalse(
            $state->active('rage')
        );
    }

    public function testConditionStateCanActivateAndEndRageImmutably(): void
    {
        $fresh = ActiveClassConditionState::fresh();
        $active = $fresh->activate('rage');
        $ended = $active->deactivate('rage');

        self::assertFalse(
            $fresh->active('rage')
        );

        self::assertTrue(
            $active->active('rage')
        );

        self::assertFalse(
            $ended->active('rage')
        );
    }

    public function testEnteringRageSpendsOneReserveAndActivatesRage(): void
    {
        $next = (
            new BarbarianRageReserveService()
        )->enter(
            $this->barbarian(3),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
        );

        self::assertSame(
            1,
            $next['resources']->expended(
                'rage'
            )
        );

        self::assertTrue(
            $next['conditions']->active(
                'rage'
            )
        );

        self::assertSame(
            2,
            $next['resources']->remaining(
                'rage',
                3
            )
        );
    }

    public function testCannotEnterRageTwiceWhileAlreadyRaging(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BarbarianRageReserveService()
        )->enter(
            $this->barbarian(3),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );
    }

    public function testCannotEnterRageWithNoReserveRemaining(): void
    {
        $resources = ActiveClassResourceState::fresh()
            ->spend('rage', 2)
            ->spend('rage', 2);

        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BarbarianRageReserveService()
        )->enter(
            $this->barbarian(1),
            $resources,
            ActiveClassConditionState::fresh()
        );
    }

    public function testEndingRageDoesNotRefundSpentReserve(): void
    {
        $service =
            new BarbarianRageReserveService();

        $entered = $service->enter(
            $this->barbarian(3),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
        );

        $ended = $service->end(
            $this->barbarian(3),
            $entered['conditions']
        );

        self::assertFalse(
            $ended->active('rage')
        );

        self::assertSame(
            1,
            $entered['resources']->expended(
                'rage'
            )
        );
    }

    public function testLongRestRestoresRageAndEndsActiveState(): void
    {
        $service =
            new BarbarianRageReserveService();

        $rested = $service->longRest(
            $this->barbarian(6),
            ActiveClassResourceState::fromArray([
                'rage' => 3,
            ]),
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );

        self::assertSame(
            0,
            $rested['resources']->expended(
                'rage'
            )
        );

        self::assertFalse(
            $rested['conditions']->active(
                'rage'
            )
        );
    }

    public function testRageMaximumUsesScaleWithCertifiedLevel(): void
    {
        $service =
            new BarbarianRageReserveService();

        foreach ([
            1 => 2,
            3 => 3,
            6 => 4,
            12 => 5,
            17 => 6,
        ] as $level => $maximum) {
            self::assertSame(
                $maximum,
                $service->maximum(
                    $this->barbarian($level)
                )
            );
        }
    }

    public function testLevelTwentyRageIsUnlimitedAndDoesNotSpendReserve(): void
    {
        $service =
            new BarbarianRageReserveService();

        self::assertTrue(
            $service->unlimited(
                $this->barbarian(20)
            )
        );

        $next = $service->enter(
            $this->barbarian(20),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
        );

        self::assertSame(
            0,
            $next['resources']->expended(
                'rage'
            )
        );

        self::assertTrue(
            $next['conditions']->active(
                'rage'
            )
        );
    }

    public function testRageRegisterShowsRemainingAndActiveState(): void
    {
        $state = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->barbarian(3),
            ActiveClassResourceState::fromArray([
                'rage' => 1,
            ]),
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );

        self::assertSame(
            3,
            $state['rage']['maximum']
        );

        self::assertSame(
            2,
            $state['rage']['remaining']
        );

        self::assertSame(
            1,
            $state['rage']['expended']
        );

        self::assertTrue(
            $state['rage']['active']
        );
    }

    public function testActiveConditionRepositoryIsOwnerScoped(): void
    {
        $source = $this->source(
            'app/Modules/Characters/ActivePlay/'
            . 'Repositories/'
            . 'ActiveClassConditionRepository.php'
        );

        self::assertStringContainsString(
            "'author' => get_current_user_id()",
            $source
        );

        self::assertStringContainsString(
            "'_gmrc_active_class_conditions'",
            $source
        );
    }

    public function testRageRoutesExposeEnterEndAndLongRestCommands(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            "'/characters/{id}/rage/enter'",
            $source
        );

        self::assertStringContainsString(
            "'/characters/{id}/rage/end'",
            $source
        );

        self::assertStringContainsString(
            "'/characters/{id}/rage/rest'",
            $source
        );
    }

    public function testRageCommandsUseDedicatedNonceContract(): void
    {
        $source = $this->source(
            'app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            '#^characters/([^/]+)/rage/(?:enter|end|rest)$#',
            $source
        );

        self::assertStringContainsString(
            "'gmrc_character_rage_'",
            $source
        );
    }

    public function testLedgerProvidesEnterEndAndRestRageControls(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            '🔥 Enter Rage',
            $source
        );

        self::assertStringContainsString(
            'End Rage',
            $source
        );

        self::assertStringContainsString(
            'Take Long Rest',
            $source
        );

        self::assertStringContainsString(
            'data-rage-active=',
            $source
        );

        self::assertStringContainsString(
            'Rages remaining',
            $source
        );
    }

    public function testRagePresentationHonoursReducedMotionAndForcedColours(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-rage-register__controls.is-raging',
            $source
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $source
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $source
        );
    }

    private function barbarian(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Rage Reserve Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'barbarian'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(28),
            AbilityScores::average()
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
