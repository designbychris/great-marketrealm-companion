<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplinePolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplineRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MonkDisciplineRegisterRegressionTest extends TestCase
{
    public function testNonMonkIsUnsupported(): void
    {
        self::assertFalse(
            (new MonkDisciplineRegisterPresenter())
                ->present($this->character('fighter', 5))['supported']
        );
    }

    public function testDisciplineUnlocksAtTwoAndScalesWithLevel(): void
    {
        $policy = new MonkDisciplinePolicy();

        self::assertSame(0, $policy->maximum($this->monk(1)));
        self::assertSame(2, $policy->maximum($this->monk(2)));
        self::assertSame(5, $policy->maximum($this->monk(5)));
        self::assertSame(20, $policy->maximum($this->monk(20)));
    }

    public function testDisciplineSaveDcUsesWisdomAndProficiency(): void
    {
        $monk = $this->monk(5);

        self::assertSame(
            8
            + $monk->proficiencyBonus()->value()
            + $monk->abilityScores()->wisdom()->modifier(),
            (new MonkDisciplinePolicy())->saveDc($monk)
        );
    }

    public function testMovementBonusScalesAtCertifiedBreakpoints(): void
    {
        $policy = new MonkDisciplinePolicy();

        foreach ([
            1 => 0,
            2 => 10,
            6 => 15,
            10 => 20,
            14 => 25,
            18 => 30,
        ] as $level => $bonus) {
            self::assertSame(
                $bonus,
                $policy->movementBonusFeet($this->monk($level))
            );
        }
    }

    public function testCoreFeaturesUnlockAtExpectedLevels(): void
    {
        $presenter = new MonkDisciplineRegisterPresenter();

        self::assertFalse(
            $this->feature($presenter->present($this->monk(4)), 'stunning-strike')['unlocked']
        );
        self::assertTrue(
            $this->feature($presenter->present($this->monk(5)), 'stunning-strike')['unlocked']
        );
        self::assertTrue(
            $this->feature($presenter->present($this->monk(7)), 'evasion')['unlocked']
        );
        self::assertTrue(
            $this->feature($presenter->present($this->monk(14)), 'diamond-soul')['unlocked']
        );
    }

    public function testMonasticWayGateAndNextMilestoneAreVisible(): void
    {
        $levelTwo = (new MonkDisciplineRegisterPresenter())->present($this->monk(2));
        $levelThree = (new MonkDisciplineRegisterPresenter())->present($this->monk(3));

        self::assertSame('Opens at Level 3', $levelTwo['way']['label']);
        self::assertSame('Awaiting Monastic Way', $levelThree['way']['label']);
        self::assertSame(3, $levelTwo['next_milestone']['level']);
    }

    public function testPolicyRejectsAnotherCalling(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MonkDisciplinePolicy())
            ->maximum($this->character('rogue', 5));
    }

    public function testControllerAndLedgerReceiveDisciplineRegister(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'MonkDisciplineRegisterPresenter',
            $controller
        );
        self::assertStringContainsString(
            "'disciplineRegister' => \$disciplineRegister",
            $controller
        );
        self::assertStringContainsString(
            'The Discipline Register',
            $view
        );
        self::assertStringContainsString(
            'data-discipline-register',
            $view
        );
    }

    public function testDisciplineRegisterCanBeExtendedByLaterReserveSlice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-discipline-register',
            $view
        );

        self::assertStringContainsString(
            'data-discipline-spend',
            $view
        );
    }

    private function feature(array $state, string $key): array
    {
        foreach ($state['features'] as $feature) {
            if (($feature['key'] ?? '') === $key) {
                return $feature;
            }
        }

        self::fail('Expected Monk feature was not present.');
    }

    private function monk(int $level): Character
    {
        return $this->character('monk', $level);
    }

    private function character(string $class, int $level): Character
    {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Discipline Tester'),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average()
        );
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
