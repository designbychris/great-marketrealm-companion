<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\FighterBattleReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FighterBattleReservesRegressionTest extends TestCase
{
    public function testFreshResourceStateHasNoExpenditure(): void
    {
        $state = ActiveClassResourceState::fresh();

        self::assertSame(
            0,
            $state->expended('action-surge')
        );

        self::assertSame(
            2,
            $state->remaining(
                'action-surge',
                2
            )
        );
    }

    public function testSpendingResourceReducesRemainingWithoutMutatingOriginal(): void
    {
        $fresh = ActiveClassResourceState::fresh();

        $spent = $fresh->spend(
            'action-surge',
            2
        );

        self::assertSame(
            0,
            $fresh->expended('action-surge')
        );

        self::assertSame(
            1,
            $spent->expended('action-surge')
        );

        self::assertSame(
            1,
            $spent->remaining(
                'action-surge',
                2
            )
        );
    }

    public function testResourceCannotBeOverspent(): void
    {
        $state = ActiveClassResourceState::fresh()
            ->spend(
                'second-wind',
                1
            );

        $this->expectException(
            InvalidArgumentException::class
        );

        $state->spend(
            'second-wind',
            1
        );
    }

    public function testLevelTwoFighterCanSpendActionSurge(): void
    {
        $service =
            new FighterBattleReserveService();

        $state = $service->spend(
            $this->fighter(2),
            ActiveClassResourceState::fresh(),
            'action-surge'
        );

        self::assertSame(
            1,
            $state->expended('action-surge')
        );
    }

    public function testLockedResourceCannotBeSpent(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new FighterBattleReserveService()
        )->spend(
            $this->fighter(8),
            ActiveClassResourceState::fresh(),
            'indomitable'
        );
    }

    public function testLevelSeventeenActionSurgeHasTwoUses(): void
    {
        $service =
            new FighterBattleReserveService();

        self::assertSame(
            2,
            $service->maximum(
                $this->fighter(17),
                'action-surge'
            )
        );
    }

    public function testIndomitableMaximumTracksCertifiedLevel(): void
    {
        $service =
            new FighterBattleReserveService();

        self::assertSame(
            1,
            $service->maximum(
                $this->fighter(9),
                'indomitable'
            )
        );

        self::assertSame(
            2,
            $service->maximum(
                $this->fighter(13),
                'indomitable'
            )
        );

        self::assertSame(
            3,
            $service->maximum(
                $this->fighter(17),
                'indomitable'
            )
        );
    }

    public function testShortRestRestoresSecondWindAndActionSurgeOnly(): void
    {
        $state = ActiveClassResourceState::fromArray([
            'second-wind' => 1,
            'action-surge' => 1,
            'indomitable' => 1,
        ]);

        $restored = (
            new FighterBattleReserveService()
        )->shortRest(
            $this->fighter(17),
            $state
        );

        self::assertSame(
            0,
            $restored->expended(
                'second-wind'
            )
        );

        self::assertSame(
            0,
            $restored->expended(
                'action-surge'
            )
        );

        self::assertSame(
            1,
            $restored->expended(
                'indomitable'
            )
        );
    }

    public function testLongRestRestoresAllBattleReserves(): void
    {
        $state = ActiveClassResourceState::fromArray([
            'second-wind' => 1,
            'action-surge' => 2,
            'indomitable' => 3,
        ]);

        $restored = (
            new FighterBattleReserveService()
        )->longRest(
            $this->fighter(17),
            $state
        );

        self::assertSame(
            [],
            $restored->toArray()
        );
    }

    public function testMartialRegisterShowsCurrentRemainingUses(): void
    {
        $state = ActiveClassResourceState::fromArray([
            'action-surge' => 1,
        ]);

        $register = (
            new FighterMartialRegisterPresenter()
        )->present(
            $this->fighter(17),
            $state
        );

        self::assertSame(
            1,
            $register['resources'][1]['remaining']
        );

        self::assertSame(
            2,
            $register['resources'][1]['maximum']
        );

        self::assertSame(
            1,
            $register['resources'][1]['expended']
        );
    }

    public function testRepositoryUsesOwnerScopedCharacterMetadata(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/ActivePlay/'
            . 'Repositories/'
            . 'ActiveClassResourceRepository.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            "'author' => get_current_user_id()",
            $source
        );

        self::assertStringContainsString(
            "'_gmrc_active_class_resources'",
            $source
        );

        self::assertStringContainsString(
            "'_gmrc_character_id'",
            $source
        );
    }

    public function testCharacterRoutesExposeSpendAndRefreshCommands(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            "'/characters/{id}/resources/spend'",
            $source
        );

        self::assertStringContainsString(
            "'/characters/{id}/resources/refresh'",
            $source
        );
    }

    public function testAdminPostKnowsBattleReserveNonceContract(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            '#^characters/([^/]+)/resources/(?:spend|refresh)$#',
            $source
        );

        self::assertStringContainsString(
            "'gmrc_character_resources_'",
            $source
        );
    }

    public function testMartialRegisterRendersSpendAndRestControls(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'Spend 1 Use',
            $view
        );

        self::assertStringContainsString(
            'Reserve Spent',
            $view
        );

        self::assertStringContainsString(
            'Battle Reserve Refresh',
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
            'gmrc_character_resources_',
            $view
        );
    }

    public function testBattleReserveControlsReturnToAbilitiesTab(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($source);

        self::assertGreaterThanOrEqual(
            4,
            substr_count(
                $source,
                "'arcana'"
            )
        );
    }

    private function fighter(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Reserve Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'fighter'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
