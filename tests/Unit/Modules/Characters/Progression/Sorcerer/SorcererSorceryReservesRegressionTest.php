<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Sorcerer;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SorcererSorceryReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SorcererSorceryReservesRegressionTest extends TestCase
{
    public function testSorceryPointMaximumMatchesSorcererLevel(): void
    {
        $service =
            new SorcererSorceryReserveService();

        self::assertSame(
            2,
            $service->maximum(
                $this->sorcerer(2)
            )
        );

        self::assertSame(
            20,
            $service->maximum(
                $this->sorcerer(20)
            )
        );
    }

    public function testSorceryPointSpendPersistsAsExpenditure(): void
    {
        $service =
            new SorcererSorceryReserveService();

        $state = $service->spend(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            3
        );

        self::assertSame(
            2,
            $service->remaining(
                $this->sorcerer(5),
                $state
            )
        );

        self::assertSame(
            3,
            $service->expended(
                $state
            )
        );
    }

    public function testSorceryPointSpendCannotExceedReserve(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->spend(
            $this->sorcerer(3),
            ActiveClassResourceState::fresh(),
            4
        );
    }

    public function testFontOfMagicIsLockedBeforeLevelTwo(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->spend(
            $this->sorcerer(1),
            ActiveClassResourceState::fresh()
        );
    }

    public function testLongRestRestoresSorceryPoints(): void
    {
        $service =
            new SorcererSorceryReserveService();

        $spent = $service->spend(
            $this->sorcerer(6),
            ActiveClassResourceState::fresh(),
            4
        );

        $rested = $service->longRest(
            $this->sorcerer(6),
            $spent
        );

        self::assertSame(
            6,
            $service->remaining(
                $this->sorcerer(6),
                $rested
            )
        );
    }

    public function testSpellSlotCreationCostsMatchFontOfMagicTable(): void
    {
        $service =
            new SorcererSorceryReserveService();

        self::assertSame(
            [2, 3, 5, 6, 7],
            array_column(
                $service
                    ->slotCreationCosts(),
                'cost'
            )
        );
    }

    public function testCreatingLevelOneSlotCostsTwoSorceryPoints(): void
    {
        $service =
            new SorcererSorceryReserveService();
        $sorcerer =
            $this->sorcerer(5);

        /*
         * One Level 1 slot has already been spent.
         */
        $state =
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ]);

        $next = $service->createSpellSlot(
            $sorcerer,
            $state,
            1
        );

        self::assertSame(
            3,
            $service->remaining(
                $sorcerer,
                $next
            )
        );

        self::assertSame(
            0,
            $next->expended(
                'spell-slot-1'
            )
        );
    }

    public function testCreatedSpellSlotCannotExceedCertifiedSlotMaximum(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->createSpellSlot(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            1
        );
    }

    public function testFontOfMagicCannotCreateSlotsAboveLevelFive(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->createSpellSlot(
            $this->sorcerer(12),
            ActiveClassResourceState::fromArray([
                'spell-slot-6' => 1,
            ]),
            6
        );
    }

    public function testConvertingSpellSlotRecoversSorceryPoints(): void
    {
        $service =
            new SorcererSorceryReserveService();
        $sorcerer =
            $this->sorcerer(5);

        $state = $service->spend(
            $sorcerer,
            ActiveClassResourceState::fresh(),
            3
        );

        $next = $service->convertSpellSlot(
            $sorcerer,
            $state,
            2
        );

        self::assertSame(
            4,
            $service->remaining(
                $sorcerer,
                $next
            )
        );

        self::assertSame(
            1,
            $next->expended(
                'spell-slot-2'
            )
        );
    }

    public function testSpellSlotConversionCannotOverflowSorceryPointMaximum(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->convertSpellSlot(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            1
        );
    }

    public function testOriginRegisterReportsLiveSorceryReserve(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(7),
            ActiveClassResourceState::fromArray([
                'sorcery-points' => 3,
            ])
        );

        self::assertSame(
            7,
            $register[
                'sorcery_points'
            ]['maximum']
        );

        self::assertSame(
            4,
            $register[
                'sorcery_points'
            ]['remaining']
        );

        self::assertSame(
            3,
            $register[
                'sorcery_points'
            ]['expended']
        );
    }

    public function testControllerAndRoutesExposeSorceryReserveActions(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        foreach ([
            'spendSorceryPoints',
            'convertSorceryReserve',
            'restSorceryReserves',
        ] as $method) {
            self::assertStringContainsString(
                $method,
                $controller
            );

            self::assertStringContainsString(
                $method,
                $routes
            );
        }
    }

    public function testLedgerExposesAccessibleFontOfMagicControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Font of Magic',
            $view
        );

        self::assertStringContainsString(
            'data-sorcery-reserves',
            $view
        );

        self::assertStringContainsString(
            'data-sorcery-spend',
            $view
        );

        self::assertStringContainsString(
            'data-sorcery-convert="points-to-slot"',
            $view
        );

        self::assertStringContainsString(
            'data-sorcery-convert="slot-to-points"',
            $view
        );

        self::assertStringContainsString(
            'data-sorcery-rest',
            $view
        );
    }

    public function testSorceryReserveControlsAreResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-sorcery-reserves',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 720px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testActiveResourceRecoveryCannotCrossMaximumBoundary(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ActiveClassResourceState::fresh()
            ->recover(
                'sorcery-points'
            );
    }

    public function testSorceryReserveRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->maximum(
            $this->character(
                'wizard',
                5
            )
        );
    }

    private function sorcerer(
        int $level
    ): Character {
        return $this->character(
            'sorcerer',
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
                'Font of Magic Tester'
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
