<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplineRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MonkDisciplineReservesRegressionTest extends MonkTestCase
{
    public function testFreshLevelTwoMonkHasFullReserve(): void
    {
        $monk = $this->monk(2);
        $service = new MonkDisciplineReserveService();
        $state = ActiveClassResourceState::fresh();

        self::assertSame(2, $service->maximum($monk));
        self::assertSame(2, $service->remaining($monk, $state));
    }

    public function testSpendConsumesExactlyOneDiscipline(): void
    {
        $monk = $this->monk(5);
        $service = new MonkDisciplineReserveService();
        $state = $service->spend(
            $monk,
            ActiveClassResourceState::fresh()
        );

        self::assertSame(1, $state->expended('discipline'));
        self::assertSame(4, $service->remaining($monk, $state));
    }

    public function testDisciplineCannotBeSpentBelowZero(): void
    {
        $monk = $this->monk(2);
        $service = new MonkDisciplineReserveService();
        $state = ActiveClassResourceState::fresh();

        $state = $service->spend($monk, $state);
        $state = $service->spend($monk, $state);

        $this->expectException(InvalidArgumentException::class);
        $service->spend($monk, $state);
    }

    public function testShortRestRestoresDiscipline(): void
    {
        $monk = $this->monk(5);
        $service = new MonkDisciplineReserveService();
        $state = $service->spend(
            $monk,
            ActiveClassResourceState::fresh()
        );

        $state = $service->shortRest($monk, $state);

        self::assertSame(5, $service->remaining($monk, $state));
        self::assertSame(0, $state->expended('discipline'));
    }

    public function testLongRestRestoresDiscipline(): void
    {
        $monk = $this->monk(5);
        $service = new MonkDisciplineReserveService();
        $state = $service->spend(
            $monk,
            ActiveClassResourceState::fresh()
        );

        $state = $service->longRest($monk, $state);

        self::assertSame(5, $service->remaining($monk, $state));
    }

    public function testLevelUpReconcilesAgainstNewMaximumWithoutMutation(): void
    {
        $service = new MonkDisciplineReserveService();
        $state = ActiveClassResourceState::fromArray([
            'discipline' => 2,
        ]);

        self::assertSame(
            3,
            $service->remaining(
                $this->monk(5),
                $state
            )
        );

        self::assertSame(
            4,
            $service->remaining(
                $this->monk(6),
                $state
            )
        );
    }

    public function testLevelOneCannotSpendDiscipline(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MonkDisciplineReserveService())->spend(
            $this->monk(1),
            ActiveClassResourceState::fresh()
        );
    }

    public function testAnotherCallingCannotUseMonkReserve(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MonkDisciplineReserveService())->shortRest(
            $this->character('fighter', 5),
            ActiveClassResourceState::fresh()
        );
    }

    public function testRegisterPresentsCurrentAndMaximumReserve(): void
    {
        $monk = $this->monk(5);
        $state = ActiveClassResourceState::fromArray([
            'discipline' => 2,
        ]);

        $register = (new MonkDisciplineRegisterPresenter())
            ->present($monk, $state);

        self::assertSame(5, $register['discipline']['maximum']);
        self::assertSame(3, $register['discipline']['remaining']);
        self::assertSame(2, $register['discipline']['expended']);
        self::assertSame(
            'Short or long rest',
            $register['discipline']['refresh']
        );
    }

    public function testLedgerExposesSpendAndRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-discipline-spend',
            $view
        );
        self::assertStringContainsString(
            'data-discipline-rest="short"',
            $view
        );
        self::assertStringContainsString(
            'data-discipline-rest="long"',
            $view
        );
        self::assertStringContainsString(
            'Remaining Discipline',
            $view
        );
    }

    public function testDisciplineFormsUseApplicationPostBridgeAndNonce(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            '$appRequestUrl',
            $view
        );

        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $view
        );

        self::assertStringContainsString(
            "/discipline/spend'",
            $view
        );

        self::assertStringContainsString(
            "/discipline/rest'",
            $view
        );

        self::assertStringContainsString(
            "'gmrc_character_discipline_'",
            $view
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'discipline/(?:spend|rest)',
            $provider
        );

        self::assertStringContainsString(
            "'gmrc_character_discipline_'",
            $provider
        );
    }

    public function testControllerPersistsDisciplineMutations(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            'function spendDiscipline',
            $controller
        );
        self::assertStringContainsString(
            'function restDiscipline',
            $controller
        );
        self::assertStringContainsString(
            '/discipline/spend',
            $routes
        );
        self::assertStringContainsString(
            '/discipline/rest',
            $routes
        );
    }
}
