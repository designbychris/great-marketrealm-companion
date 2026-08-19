<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\WarlockPactReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WarlockPactReservesRegressionTest extends TestCase
{
    public function testFreshWarlockBeginsWithFullPactReserve(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(5);
        $state = ActiveClassResourceState::fresh();

        self::assertSame(
            2,
            $service->maximum($warlock)
        );

        self::assertSame(
            2,
            $service->remaining(
                $warlock,
                $state
            )
        );

        self::assertSame(
            3,
            $service->slotLevel($warlock)
        );
    }

    public function testSpendingPactSlotPersistsExpenditure(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(5);

        $state = $service->spend(
            $warlock,
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            1,
            $service->expended($state)
        );

        self::assertSame(
            1,
            $service->remaining(
                $warlock,
                $state
            )
        );
    }

    public function testPactSlotsCannotBeOverspent(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(2);
        $state = ActiveClassResourceState::fresh();

        $state = $service->spend(
            $warlock,
            $state
        );

        $state = $service->spend(
            $warlock,
            $state
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $service->spend(
            $warlock,
            $state
        );
    }

    public function testShortRestRestoresPactMagic(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(5);

        $state = $service->spend(
            $warlock,
            ActiveClassResourceState::fresh()
        );

        $state = $service->shortRest(
            $warlock,
            $state
        );

        self::assertSame(
            0,
            $service->expended($state)
        );

        self::assertSame(
            2,
            $service->remaining(
                $warlock,
                $state
            )
        );
    }

    public function testLongRestAlsoRestoresPactMagic(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(5);

        $state = $service->spend(
            $warlock,
            ActiveClassResourceState::fresh()
        );

        $state = $service->longRest(
            $warlock,
            $state
        );

        self::assertSame(
            0,
            $service->expended($state)
        );
    }

    public function testPactRestLeavesUnrelatedResourcesUntouched(): void
    {
        $service = new WarlockPactReserveService();

        $state = ActiveClassResourceState::fromArray([
            WarlockPactReserveService::RESOURCE => 1,
            'unrelated-resource' => 2,
        ]);

        $state = $service->shortRest(
            $this->warlock(5),
            $state
        );

        self::assertSame(
            0,
            $state->expended(
                WarlockPactReserveService::RESOURCE
            )
        );

        self::assertSame(
            2,
            $state->expended(
                'unrelated-resource'
            )
        );
    }

    public function testLevelUpReconcilesAgainstLargerPactMaximum(): void
    {
        $service = new WarlockPactReserveService();

        $state = ActiveClassResourceState::fromArray([
            WarlockPactReserveService::RESOURCE => 1,
        ]);

        self::assertSame(
            1,
            $service->remaining(
                $this->warlock(10),
                $state
            )
        );

        self::assertSame(
            2,
            $service->remaining(
                $this->warlock(11),
                $state
            )
        );
    }

    public function testSlotLevelChangesWithoutChangingResourceIdentity(): void
    {
        $service = new WarlockPactReserveService();

        self::assertSame(
            1,
            $service->slotLevel(
                $this->warlock(1)
            )
        );

        self::assertSame(
            5,
            $service->slotLevel(
                $this->warlock(9)
            )
        );

        self::assertSame(
            'pact-magic-slot',
            WarlockPactReserveService::RESOURCE
        );
    }

    public function testArcaneSlotPresentationUsesOneCurrentPactPool(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(17);

        $slots = $service->presentSlots(
            $warlock,
            ActiveClassResourceState::fromArray([
                WarlockPactReserveService::RESOURCE => 1,
            ])
        );

        self::assertCount(1, $slots);
        self::assertSame(5, $slots[0]['level']);
        self::assertSame(4, $slots[0]['total']);
        self::assertSame(3, $slots[0]['remaining']);
        self::assertTrue($slots[0]['pact']);
    }

    public function testPatronRegisterCarriesPersistentPactState(): void
    {
        $register = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $this->warlock(5),
            ActiveClassResourceState::fromArray([
                WarlockPactReserveService::RESOURCE => 1,
            ])
        );

        self::assertSame(
            2,
            $register[
                'pact_magic'
            ]['slots']
        );

        self::assertSame(
            1,
            $register[
                'pact_magic'
            ]['remaining']
        );

        self::assertSame(
            1,
            $register[
                'pact_magic'
            ]['expended']
        );
    }

    public function testControllerUsesPactReserveInsteadOfSharedSlotsForWarlock(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'new WarlockPactReserveService()',
            $controller
        );

        self::assertStringContainsString(
            'function spendPactSlot',
            $controller
        );

        self::assertStringContainsString(
            'function restPactSlots',
            $controller
        );

        self::assertStringContainsString(
            "=== 'warlock'",
            $controller
        );
    }

    public function testPactRoutesUseDedicatedNonceBridge(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/pact/spend',
            $routes
        );

        self::assertStringContainsString(
            '/pact/rest',
            $routes
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'pact/(?:spend|rest)',
            $provider
        );

        self::assertStringContainsString(
            "'gmrc_character_pact_'",
            $provider
        );
    }

    public function testLedgerProvidesSpendAndBothRestControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-pact-reserves',
            $view
        );

        self::assertStringContainsString(
            'data-pact-slot-spend',
            $view
        );

        self::assertStringContainsString(
            'data-pact-rest=',
            $view
        );

        self::assertStringContainsString(
            'Spend Pact Slot',
            $view
        );

        self::assertStringContainsString(
            'Take Short Rest',
            $view
        );

        self::assertStringContainsString(
            'Take Long Rest',
            $view
        );

        self::assertStringContainsString(
            'Pact slots ready',
            $view
        );
    }

    public function testPactReservePresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-patron-register__pact-controls',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 560px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testForeignCallingCannotUsePactReserve(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new WarlockPactReserveService())
            ->spend(
                $this->character(
                    'fighter',
                    5
                ),
                ActiveClassResourceState::fresh()
            );
    }

    private function warlock(
        int $level
    ): Character {
        return $this->character(
            'warlock',
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
                'Pact Reserve Tester'
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
