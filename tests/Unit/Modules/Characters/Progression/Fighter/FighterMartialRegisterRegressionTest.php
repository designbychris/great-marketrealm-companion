<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class FighterMartialRegisterRegressionTest extends TestCase
{
    public function testNonFighterDoesNotReceiveMartialRegister(): void
    {
        $state = (new FighterMartialRegisterPresenter())
            ->present(
                $this->character(
                    'wizard',
                    4
                )
            );

        self::assertFalse(
            $state['supported']
        );
    }

    public function testLevelOneFighterStartsWithSecondWind(): void
    {
        $state = $this->fighterState(1);

        self::assertTrue(
            $state['supported']
        );

        self::assertSame(
            1,
            $state['attacks_per_action']
        );

        self::assertTrue(
            $state['resources'][0]['unlocked']
        );

        self::assertSame(
            '1d10 + 1 healing',
            $state['resources'][0]['effect']
        );

        self::assertFalse(
            $state['resources'][1]['unlocked']
        );

        self::assertFalse(
            $state['resources'][2]['unlocked']
        );
    }

    public function testLevelTwoUnlocksOneActionSurge(): void
    {
        $state = $this->fighterState(2);

        self::assertTrue(
            $state['resources'][1]['unlocked']
        );

        self::assertSame(
            1,
            $state['resources'][1]['uses']
        );

        self::assertSame(
            'Short rest',
            $state['resources'][1]['refresh']
        );
    }

    public function testExtraAttackScalesFromTwoToFourAttacks(): void
    {
        self::assertSame(
            2,
            $this->fighterState(5)[
                'attacks_per_action'
            ]
        );

        self::assertSame(
            3,
            $this->fighterState(11)[
                'attacks_per_action'
            ]
        );

        self::assertSame(
            4,
            $this->fighterState(20)[
                'attacks_per_action'
            ]
        );
    }

    public function testIndomitableScalesAcrossItsMilestones(): void
    {
        self::assertSame(
            0,
            $this->fighterState(8)[
                'resources'
            ][2]['uses']
        );

        self::assertSame(
            1,
            $this->fighterState(9)[
                'resources'
            ][2]['uses']
        );

        self::assertSame(
            2,
            $this->fighterState(13)[
                'resources'
            ][2]['uses']
        );

        self::assertSame(
            3,
            $this->fighterState(17)[
                'resources'
            ][2]['uses']
        );
    }

    public function testActionSurgeReachesTwoUsesAtSeventeen(): void
    {
        $state = $this->fighterState(17);

        self::assertSame(
            2,
            $state['resources'][1]['uses']
        );

        self::assertSame(
            3,
            $state['resources'][2]['uses']
        );
    }

    public function testMartialPathShowsItsLevelThreeGate(): void
    {
        $levelTwo = $this->fighterState(2);
        $levelThree = $this->fighterState(3);

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
            'Awaiting Martial Path',
            $levelThree['path']['label']
        );
    }

    public function testCertifiedMartialPathUsesCatalogueLabel(): void
    {
        $fighter = $this->character(
            'fighter',
            3
        );

        $fighter->chooseCallingPath(
            CallingPath::fromString(
                'the-carver'
            )
        );

        $state = (
            new FighterMartialRegisterPresenter()
        )->present($fighter);

        self::assertTrue(
            $state['path']['chosen']
        );

        self::assertSame(
            'the-carver',
            $state['path']['key']
        );

        self::assertSame(
            'The Carver',
            $state['path']['label']
        );
    }

    public function testNextMartialMilestoneMovesWithLevel(): void
    {
        self::assertSame(
            2,
            $this->fighterState(1)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            5,
            $this->fighterState(3)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            17,
            $this->fighterState(16)[
                'next_milestone'
            ]['level']
        );

        self::assertNull(
            $this->fighterState(20)[
                'next_milestone'
            ]
        );
    }

    public function testCharacterControllerSuppliesMartialRegisterToLedger(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'FighterMartialRegisterPresenter',
            $source
        );

        self::assertStringContainsString(
            "'martialRegister' => \$martialRegister",
            $source
        );
    }

    public function testCharacterLedgerRendersMartialRegisterConditionally(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'The Martial Register',
            $source
        );

        self::assertStringContainsString(
            "empty(\$martialRegister['supported'])",
            $source
        );

        self::assertStringContainsString(
            'data-martial-register',
            $source
        );

        self::assertStringContainsString(
            'Next martial milestone',
            $source
        );
    }

    public function testMartialRegisterHasResponsiveAccessiblePresentation(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            '.gmrc-martial-register',
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
    private function fighterState(
        int $level
    ): array {
        return (
            new FighterMartialRegisterPresenter()
        )->present(
            $this->character(
                'fighter',
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
                'Test Adventurer'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(12),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
