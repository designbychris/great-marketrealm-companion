<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class BarbarianRageRegisterRegressionTest extends TestCase
{
    public function testNonBarbarianDoesNotReceiveRageRegister(): void
    {
        $state = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->character(
                'fighter',
                4
            )
        );

        self::assertFalse(
            $state['supported']
        );
    }

    public function testLevelOneBarbarianStartsWithTwoRagesAndPlusTwoDamage(): void
    {
        $state = $this->barbarianState(1);

        self::assertTrue(
            $state['supported']
        );

        self::assertSame(
            2,
            $state['rage']['uses']
        );

        self::assertFalse(
            $state['rage']['unlimited']
        );

        self::assertSame(
            2,
            $state['rage']['damage_bonus']
        );

        self::assertSame(
            1,
            $state['attacks_per_action']
        );
    }

    public function testRageCapacityScalesAcrossCertifiedLevels(): void
    {
        self::assertSame(
            3,
            $this->barbarianState(3)[
                'rage'
            ]['uses']
        );

        self::assertSame(
            4,
            $this->barbarianState(6)[
                'rage'
            ]['uses']
        );

        self::assertSame(
            5,
            $this->barbarianState(12)[
                'rage'
            ]['uses']
        );

        self::assertSame(
            6,
            $this->barbarianState(17)[
                'rage'
            ]['uses']
        );
    }

    public function testLevelTwentyRageIsUnlimited(): void
    {
        $state = $this->barbarianState(20);

        self::assertTrue(
            $state['rage']['unlimited']
        );

        self::assertSame(
            0,
            $state['rage']['uses']
        );
    }

    public function testRageDamageBonusScalesAtNineAndSixteen(): void
    {
        self::assertSame(
            2,
            $this->barbarianState(8)[
                'rage'
            ]['damage_bonus']
        );

        self::assertSame(
            3,
            $this->barbarianState(9)[
                'rage'
            ]['damage_bonus']
        );

        self::assertSame(
            4,
            $this->barbarianState(16)[
                'rage'
            ]['damage_bonus']
        );
    }

    public function testExtraAttackAndFastMovementArriveAtFive(): void
    {
        $levelFour = $this->barbarianState(4);
        $levelFive = $this->barbarianState(5);

        self::assertSame(
            1,
            $levelFour['attacks_per_action']
        );

        self::assertSame(
            0,
            $levelFour['speed_bonus']
        );

        self::assertSame(
            2,
            $levelFive['attacks_per_action']
        );

        self::assertSame(
            10,
            $levelFive['speed_bonus']
        );
    }

    public function testBrutalCriticalDiceScaleAcrossMilestones(): void
    {
        self::assertSame(
            0,
            $this->barbarianState(8)[
                'brutal_critical_dice'
            ]
        );

        self::assertSame(
            1,
            $this->barbarianState(9)[
                'brutal_critical_dice'
            ]
        );

        self::assertSame(
            2,
            $this->barbarianState(13)[
                'brutal_critical_dice'
            ]
        );

        self::assertSame(
            3,
            $this->barbarianState(17)[
                'brutal_critical_dice'
            ]
        );
    }

    public function testLevelTwoUnlocksRecklessAttackAndDangerSense(): void
    {
        $features = array_filter(
            $this->barbarianState(2)[
                'features'
            ],
            static fn (
                array $feature
            ): bool =>
                ! empty(
                    $feature['unlocked']
                )
        );

        self::assertSame(
            [
                'reckless-attack',
                'danger-sense',
            ],
            array_column(
                $features,
                'key'
            )
        );
    }

    public function testPrimalPathShowsItsLevelThreeGate(): void
    {
        $levelTwo = $this->barbarianState(2);
        $levelThree = $this->barbarianState(3);

        self::assertFalse(
            $levelTwo['path']['available']
        );

        self::assertSame(
            'Opens at Level 3',
            $levelTwo['path']['label']
        );

        self::assertTrue(
            $levelThree['path']['available']
        );

        self::assertSame(
            'Awaiting Primal Path',
            $levelThree['path']['label']
        );
    }

    public function testCertifiedPrimalPathUsesCatalogueLabel(): void
    {
        $barbarian = $this->character(
            'barbarian',
            3
        );

        $barbarian->chooseCallingPath(
            CallingPath::fromString(
                'path-of-the-rind'
            )
        );

        $state = (
            new BarbarianRageRegisterPresenter()
        )->present($barbarian);

        self::assertTrue(
            $state['path']['chosen']
        );

        self::assertSame(
            'path-of-the-rind',
            $state['path']['key']
        );

        self::assertSame(
            'Path of the Rind',
            $state['path']['label']
        );
    }

    public function testNextPrimalMilestoneMovesWithLevel(): void
    {
        self::assertSame(
            2,
            $this->barbarianState(1)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            5,
            $this->barbarianState(3)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            20,
            $this->barbarianState(19)[
                'next_milestone'
            ]['level']
        );

        self::assertNull(
            $this->barbarianState(20)[
                'next_milestone'
            ]
        );
    }

    public function testControllerSuppliesRageRegisterToLedger(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'BarbarianRageRegisterPresenter',
            $source
        );

        self::assertStringContainsString(
            "'rageRegister' => \$rageRegister",
            $source
        );
    }

    public function testLedgerRendersRageRegisterConditionally(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'The Rage Register',
            $source
        );

        self::assertStringContainsString(
            "empty(\$rageRegister['supported'])",
            $source
        );

        self::assertStringContainsString(
            'data-rage-register',
            $source
        );

        self::assertStringContainsString(
            'Next primal milestone',
            $source
        );
    }

    public function testRageRegisterHasResponsiveAccessiblePresentation(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            '.gmrc-rage-register',
            $source
        );

        self::assertStringContainsString(
            '@media (max-width: 840px)',
            $source
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $source
        );
    }

    /** @return array<string,mixed> */
    private function barbarianState(
        int $level
    ): array {
        return (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->character(
                'barbarian',
                $level
            )
        );
    }

    private function character(
        string $class,
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Rage Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(24),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
