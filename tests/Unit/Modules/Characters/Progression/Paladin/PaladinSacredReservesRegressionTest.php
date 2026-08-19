<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\PaladinSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinSacredReservesRegressionTest extends TestCase
{
    public function testFreshPaladinBeginsWithFullSacredReserves(): void
    {
        $paladin = $this->paladin(4);
        $state = ActiveClassResourceState::fresh();
        $service = new PaladinSacredReserveService();

        self::assertSame(
            20,
            $service->remaining(
                $paladin,
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            $service->maximum(
                $paladin,
                PaladinSacredReserveService::DIVINE_SENSE
            ),
            $service->remaining(
                $paladin,
                $state,
                PaladinSacredReserveService::DIVINE_SENSE
            )
        );
    }

    public function testLayOnHandsCanSpendMoreThanOnePointSafely(): void
    {
        $paladin = $this->paladin(4);
        $service = new PaladinSacredReserveService();

        $state = $service->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::LAY_ON_HANDS,
            5
        );

        self::assertSame(
            5,
            $state->expended(
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            15,
            $service->remaining(
                $paladin,
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );
    }

    public function testLayOnHandsCannotOverspend(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredReserveService())
            ->spend(
                $this->paladin(1),
                ActiveClassResourceState::fresh(),
                PaladinSacredReserveService::LAY_ON_HANDS,
                6
            );
    }

    public function testDivineSenseUsesSharedPersistentResourceState(): void
    {
        $paladin = $this->paladin(4);
        $service = new PaladinSacredReserveService();

        $state = $service->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::DIVINE_SENSE
        );

        self::assertSame(
            1,
            $state->expended(
                PaladinSacredReserveService::DIVINE_SENSE
            )
        );
    }

    public function testCleansingTouchCannotSpendBeforeLevelFourteen(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredReserveService())
            ->spend(
                $this->paladin(13),
                ActiveClassResourceState::fresh(),
                PaladinSacredReserveService::CLEANSING_TOUCH
            );
    }

    public function testCleansingTouchCanSpendAtLevelFourteen(): void
    {
        $service = new PaladinSacredReserveService();

        $state = $service->spend(
            $this->paladin(14),
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::CLEANSING_TOUCH
        );

        self::assertSame(
            1,
            $state->expended(
                PaladinSacredReserveService::CLEANSING_TOUCH
            )
        );
    }

    public function testLongRestRestoresOnlyPaladinSacredResources(): void
    {
        $paladin = $this->paladin(14);
        $service = new PaladinSacredReserveService();

        $state = ActiveClassResourceState::fromArray([
            PaladinSacredReserveService::LAY_ON_HANDS => 4,
            PaladinSacredReserveService::DIVINE_SENSE => 1,
            PaladinSacredReserveService::CLEANSING_TOUCH => 1,
            'unrelated-resource' => 2,
        ]);

        $rested = $service->longRest(
            $paladin,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                PaladinSacredReserveService::DIVINE_SENSE
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                PaladinSacredReserveService::CLEANSING_TOUCH
            )
        );

        self::assertSame(
            2,
            $rested->expended(
                'unrelated-resource'
            )
        );
    }

    public function testLevelUpReconcilesAgainstNewLayOnHandsMaximum(): void
    {
        $service = new PaladinSacredReserveService();
        $state = ActiveClassResourceState::fromArray([
            PaladinSacredReserveService::LAY_ON_HANDS => 5,
        ]);

        self::assertSame(
            15,
            $service->remaining(
                $this->paladin(4),
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            20,
            $service->remaining(
                $this->paladin(5),
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );
    }

    public function testUnknownSacredReserveIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredReserveService())
            ->spend(
                $this->paladin(5),
                ActiveClassResourceState::fresh(),
                'mystery-blessing'
            );
    }

    public function testAnotherCallingCannotUsePaladinSacredReserves(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredReserveService())
            ->longRest(
                $this->character('fighter', 5),
                ActiveClassResourceState::fresh()
            );
    }

    public function testRegisterPresentsRemainingAndMaximumTogether(): void
    {
        $state = ActiveClassResourceState::fromArray([
            PaladinSacredReserveService::LAY_ON_HANDS => 3,
            PaladinSacredReserveService::DIVINE_SENSE => 1,
        ]);

        $register = (
            new PaladinSacredRegisterPresenter()
        )->present(
            $this->paladin(4),
            $state
        );

        self::assertSame(
            17,
            $register[
                'lay_on_hands'
            ]['remaining']
        );

        self::assertSame(
            20,
            $register[
                'lay_on_hands'
            ]['maximum']
        );

        self::assertSame(
            3,
            $register[
                'lay_on_hands'
            ]['expended']
        );

        self::assertSame(
            1,
            $register[
                'divine_sense'
            ]['expended']
        );
    }

    public function testLedgerExposesSacredSpendAndLongRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-sacred-spend',
            $view
        );

        self::assertStringContainsString(
            "'key' => 'lay-on-hands'",
            $view
        );

        self::assertStringContainsString(
            "'key' => 'divine-sense'",
            $view
        );

        self::assertStringContainsString(
            "'key' => 'cleansing-touch'",
            $view
        );

        self::assertStringContainsString(
            'data-sacred-rest',
            $view
        );

        self::assertStringContainsString(
            'Lay on Hands remaining',
            $view
        );
    }

    public function testSacredFormsUseApplicationPostBridgeAndNonce(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $view
        );

        self::assertStringContainsString(
            "/sacred/spend'",
            $view
        );

        self::assertStringContainsString(
            "/sacred/rest'",
            $view
        );

        self::assertStringContainsString(
            "'gmrc_character_sacred_'",
            $view
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'sacred/(?:spend|rest)',
            $provider
        );

        self::assertStringContainsString(
            "'gmrc_character_sacred_'",
            $provider
        );
    }

    public function testControllerPersistsSacredReserveMutations(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            'function spendSacredReserve',
            $controller
        );

        self::assertStringContainsString(
            'function restSacredReserves',
            $controller
        );

        self::assertStringContainsString(
            '/sacred/spend',
            $routes
        );

        self::assertStringContainsString(
            '/sacred/rest',
            $routes
        );
    }

    private function paladin(
        int $level
    ): Character {
        return $this->character(
            'paladin',
            $level
        );
    }

    private function character(
        string $class,
        int $level
    ): Character {
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
