<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\PaladinSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinSacredActionsRegressionTest extends TestCase
{
    public function testLevelOneHasLayOnHandsAndDivineSenseButNoSmite(): void
    {
        $state = (new PaladinSacredActionPresenter())
            ->present(
                $this->paladin(1),
                ActiveClassResourceState::fresh()
            );

        self::assertTrue(
            $state['lay_on_hands']['available']
        );

        self::assertTrue(
            $state['divine_sense']['available']
        );

        self::assertFalse(
            $state['divine_smite']['unlocked']
        );

        self::assertSame(
            [],
            $state['divine_smite']['smite_options']
        );
    }

    public function testLevelTwoSmiteOptionsUseRealPaladinSpellSlots(): void
    {
        $state = (new PaladinSacredActionPresenter())
            ->present(
                $this->paladin(2),
                ActiveClassResourceState::fresh()
            );

        self::assertTrue(
            $state['divine_smite']['unlocked']
        );

        self::assertSame(
            [1],
            array_column(
                $state['divine_smite']['smite_options'],
                'slot_level'
            )
        );

        self::assertSame(
            '2d8',
            $state['divine_smite']['smite_options'][0]['formula']
        );
    }

    public function testSmiteDamageScalesBySlotAndCapsAtFiveDice(): void
    {
        $state = (new PaladinSacredActionPresenter())
            ->present(
                $this->paladin(17),
                ActiveClassResourceState::fresh()
            );

        $formulae = [];

        foreach (
            $state['divine_smite']['smite_options']
            as $option
        ) {
            $formulae[
                $option['slot_level']
            ] = $option['formula'];
        }

        self::assertSame('2d8', $formulae[1]);
        self::assertSame('3d8', $formulae[2]);
        self::assertSame('4d8', $formulae[3]);
        self::assertSame('5d8', $formulae[4]);
        self::assertSame('5d8', $formulae[5]);
    }

    public function testSmiteSpendsSharedSpellSlotResource(): void
    {
        $paladin = $this->paladin(5);
        $slots = new SharedSpellSlotReserveService();

        $state = $slots->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            1
        );

        self::assertSame(
            1,
            $state->expended('spell-slot-1')
        );

        self::assertSame(
            $slots->maximum($paladin, 1) - 1,
            $slots->remaining(
                $paladin,
                $state,
                1
            )
        );
    }

    public function testSpellSlotCannotBeOverspent(): void
    {
        $paladin = $this->paladin(2);
        $slots = new SharedSpellSlotReserveService();
        $state = ActiveClassResourceState::fresh();

        for (
            $use = 0;
            $use < $slots->maximum($paladin, 1);
            $use++
        ) {
            $state = $slots->spend(
                $paladin,
                $state,
                1
            );
        }

        $this->expectException(
            InvalidArgumentException::class
        );

        $slots->spend(
            $paladin,
            $state,
            1
        );
    }

    public function testUnavailableSlotLevelIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new SharedSpellSlotReserveService())
            ->spend(
                $this->paladin(2),
                ActiveClassResourceState::fresh(),
                2
            );
    }

    public function testLongRestRestoresSacredReservesAndSpellSlots(): void
    {
        $paladin = $this->paladin(5);
        $sacred = new PaladinSacredReserveService();
        $slots = new SharedSpellSlotReserveService();

        $state = $sacred->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::LAY_ON_HANDS,
            3
        );

        $state = $slots->spend(
            $paladin,
            $state,
            1
        );

        $state = $sacred->longRest(
            $paladin,
            $state
        );

        $state = $slots->longRest(
            $paladin,
            $state
        );

        self::assertSame(
            0,
            $state->expended(
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            0,
            $state->expended('spell-slot-1')
        );
    }

    public function testActionPresenterReflectsSpentSharedSlot(): void
    {
        $paladin = $this->paladin(5);
        $slots = new SharedSpellSlotReserveService();

        $state = $slots->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            1
        );

        $presented = (new PaladinSacredActionPresenter())
            ->present(
                $paladin,
                $state
            );

        self::assertSame(
            $slots->remaining(
                $paladin,
                $state,
                1
            ),
            $presented['divine_smite']['smite_options'][0]['remaining']
        );
    }

    public function testCleansingTouchActionRemainsLockedBeforeFourteen(): void
    {
        $state = (new PaladinSacredActionPresenter())
            ->present(
                $this->paladin(13),
                ActiveClassResourceState::fresh()
            );

        self::assertFalse(
            $state['cleansing_touch']['unlocked']
        );

        self::assertFalse(
            $state['cleansing_touch']['available']
        );
    }

    public function testRegisterCarriesNamedSacredActions(): void
    {
        $register = (new PaladinSacredRegisterPresenter())
            ->present(
                $this->paladin(5),
                ActiveClassResourceState::fresh()
            );

        self::assertTrue(
            $register['actions']['supported']
        );

        self::assertTrue(
            $register['actions']['divine_smite']['unlocked']
        );
    }

    public function testLedgerOffersVariableLayOnHandsAndRecipientChoice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Sacred Actions',
            $view
        );

        self::assertStringContainsString(
            'name="amount"',
            $view
        );

        self::assertStringContainsString(
            'name="target"',
            $view
        );

        self::assertStringContainsString(
            'Heal this Paladin',
            $view
        );

        self::assertStringContainsString(
            'Record spend for another creature',
            $view
        );
    }

    public function testLedgerSmiteUsesSharedSlotAndGuildDiceworks(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-sacred-action="divine-smite"',
            $view
        );

        self::assertStringContainsString(
            'name="slot_level"',
            $view
        );

        self::assertStringContainsString(
            'Roll Smite',
            $view
        );

        self::assertStringContainsString(
            'gmrc-guild-roll-trigger',
            $view
        );

        self::assertStringContainsString(
            'data-roll-damage-type="radiant"',
            $view
        );
    }

    public function testControllerResolvesNamedSacredActions(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'function useSacredAction',
            $controller
        );

        self::assertStringContainsString(
            "\$_POST['sacred_action']",
            $controller
        );

        self::assertStringContainsString(
            'SharedSpellSlotReserveService',
            $controller
        );

        self::assertStringContainsString(
            '$character->heal($amount)',
            $controller
        );
    }

    public function testSacredActionRouteUsesExistingNonceBridge(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/sacred/action',
            $routes
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'sacred/(?:action|spend|rest)',
            $provider
        );
    }

    public function testArcanePantryShowsRemainingAndTotalSharedSlots(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            "\$slot['remaining']",
            $view
        );

        self::assertStringContainsString(
            "\$slot['total']",
            $view
        );
    }

    public function testSacredActionsRemainResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-sacred-actions',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 840px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    private function paladin(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Sacred Action Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'paladin'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(30),
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
