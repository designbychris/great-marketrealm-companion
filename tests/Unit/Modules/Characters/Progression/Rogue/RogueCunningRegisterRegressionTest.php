<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class RogueCunningRegisterRegressionTest extends TestCase
{
    public function testNonRogueDoesNotReceiveCunningRegister(): void
    {
        $state = (
            new RogueCunningRegisterPresenter()
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

    public function testLevelOneRogueStartsAtOneD6SneakAttack(): void
    {
        $state = $this->rogueState(1);

        self::assertTrue(
            $state['supported']
        );

        self::assertSame(
            '1d6',
            $state['sneak_attack']['dice']
        );

        self::assertSame(
            'Once per turn',
            $state['sneak_attack']['frequency']
        );

        self::assertFalse(
            $state['cunning_action']['unlocked']
        );
    }

    public function testSneakAttackDiceScaleAcrossRogueLevels(): void
    {
        foreach ([
            1 => '1d6',
            3 => '2d6',
            5 => '3d6',
            9 => '5d6',
            11 => '6d6',
            19 => '10d6',
            20 => '10d6',
        ] as $level => $dice) {
            self::assertSame(
                $dice,
                $this->rogueState($level)[
                    'sneak_attack'
                ]['dice']
            );
        }
    }

    public function testCunningActionUnlocksAtLevelTwoWithThreeOptions(): void
    {
        $state = $this->rogueState(2);

        self::assertTrue(
            $state['cunning_action']['unlocked']
        );

        self::assertSame(
            [
                'Dash',
                'Disengage',
                'Hide',
            ],
            $state[
                'cunning_action'
            ]['options']
        );
    }

    public function testUncannyDodgeUnlocksAtFive(): void
    {
        self::assertFalse(
            $this->feature(
                4,
                'uncanny-dodge'
            )['unlocked']
        );

        self::assertTrue(
            $this->feature(
                5,
                'uncanny-dodge'
            )['unlocked']
        );
    }

    public function testEvasionUnlocksAtSeven(): void
    {
        self::assertFalse(
            $this->feature(
                6,
                'evasion'
            )['unlocked']
        );

        self::assertTrue(
            $this->feature(
                7,
                'evasion'
            )['unlocked']
        );
    }

    public function testReliableTalentUnlocksAtEleven(): void
    {
        self::assertFalse(
            $this->feature(
                10,
                'reliable-talent'
            )['unlocked']
        );

        self::assertTrue(
            $this->feature(
                11,
                'reliable-talent'
            )['unlocked']
        );
    }

    public function testLaterFeaturesKeepCertifiedLevelBoundaries(): void
    {
        foreach ([
            'blindsense' => 14,
            'slippery-mind' => 15,
            'elusive' => 18,
            'stroke-of-luck' => 20,
        ] as $key => $level) {
            self::assertFalse(
                $this->feature(
                    $level - 1,
                    $key
                )['unlocked']
            );

            self::assertTrue(
                $this->feature(
                    $level,
                    $key
                )['unlocked']
            );
        }
    }

    public function testArchetypeShowsItsLevelThreeGate(): void
    {
        $levelTwo = $this->rogueState(2);
        $levelThree = $this->rogueState(3);

        self::assertFalse(
            $levelTwo['archetype']['available']
        );

        self::assertSame(
            'Opens at Level 3',
            $levelTwo['archetype']['label']
        );

        self::assertTrue(
            $levelThree['archetype']['available']
        );

        self::assertSame(
            'Awaiting Rogue Archetype',
            $levelThree['archetype']['label']
        );
    }

    public function testCertifiedArchetypeUsesRepositoryCatalogueLabel(): void
    {
        $rogue = $this->character(
            'rogue',
            3
        );

        $rogue->chooseCallingPath(
            CallingPath::fromString(
                'mastermind-of-the-aisles'
            )
        );

        $state = (
            new RogueCunningRegisterPresenter()
        )->present($rogue);

        self::assertTrue(
            $state['archetype']['chosen']
        );

        self::assertSame(
            'mastermind-of-the-aisles',
            $state['archetype']['key']
        );

        self::assertSame(
            'Mastermind of the Aisles',
            $state['archetype']['label']
        );
    }

    public function testNextCunningMilestoneMovesWithLevel(): void
    {
        self::assertSame(
            2,
            $this->rogueState(1)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            5,
            $this->rogueState(3)[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            20,
            $this->rogueState(19)[
                'next_milestone'
            ]['level']
        );

        self::assertNull(
            $this->rogueState(20)[
                'next_milestone'
            ]
        );
    }

    public function testControllerSuppliesCunningRegisterToLedger(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'RogueCunningRegisterPresenter',
            $source
        );

        self::assertStringContainsString(
            "'cunningRegister' => \$cunningRegister",
            $source
        );
    }

    public function testLedgerRendersCunningRegisterConditionally(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Cunning Register',
            $source
        );

        self::assertStringContainsString(
            "empty(\$cunningRegister['supported'])",
            $source
        );

        self::assertStringContainsString(
            'data-cunning-register',
            $source
        );

        self::assertStringContainsString(
            'Next cunning milestone',
            $source
        );
    }

    public function testCunningRegisterKeepsSneakAttackReadOnlyInThisSlice(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Sneak Attack is ready for this turn.',
            $source
        );

        self::assertStringNotContainsString(
            'data-sneak-attack-spent',
            $source
        );
    }

    public function testCunningRegisterHasResponsiveAccessiblePresentation(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-cunning-register',
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

    /**
     * @return array<string,mixed>
     */
    private function feature(
        int $level,
        string $key
    ): array {
        foreach (
            $this->rogueState($level)[
                'features'
            ]
            as $feature
        ) {
            if (
                ($feature['key'] ?? '')
                === $key
            ) {
                return $feature;
            }
        }

        self::fail(
            'Expected Rogue feature was not present.'
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function rogueState(
        int $level
    ): array {
        return (
            new RogueCunningRegisterPresenter()
        )->present(
            $this->character(
                'rogue',
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
                'Cunning Register Tester'
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
