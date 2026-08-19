<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WarlockPatronRegisterRegressionTest extends TestCase
{
    public function testNonWarlockIsUnsupported(): void
    {
        self::assertFalse(
            (new WarlockPatronRegisterPresenter())
                ->present(
                    $this->character('fighter', 3)
                )['supported']
        );
    }

    public function testPactSlotLevelProgressionIsLevelAware(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(1, $policy->pactSlotLevel($this->warlock(1)));
        self::assertSame(2, $policy->pactSlotLevel($this->warlock(3)));
        self::assertSame(3, $policy->pactSlotLevel($this->warlock(5)));
        self::assertSame(4, $policy->pactSlotLevel($this->warlock(7)));
        self::assertSame(5, $policy->pactSlotLevel($this->warlock(9)));
    }

    public function testPactSlotCountReachesFourAtSeventeen(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(1, $policy->pactSlots($this->warlock(1)));
        self::assertSame(2, $policy->pactSlots($this->warlock(2)));
        self::assertSame(3, $policy->pactSlots($this->warlock(11)));
        self::assertSame(4, $policy->pactSlots($this->warlock(17)));
    }

    public function testInvocationCadenceIsLevelAware(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(0, $policy->invocationsKnown($this->warlock(1)));
        self::assertSame(2, $policy->invocationsKnown($this->warlock(2)));
        self::assertSame(3, $policy->invocationsKnown($this->warlock(5)));
        self::assertSame(5, $policy->invocationsKnown($this->warlock(9)));
        self::assertSame(8, $policy->invocationsKnown($this->warlock(18)));
    }

    public function testMysticArcanumUnlocksAtCorrectMilestones(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(
            [],
            $policy->mysticArcanumLevels(
                $this->warlock(10)
            )
        );

        self::assertSame(
            [6],
            $policy->mysticArcanumLevels(
                $this->warlock(11)
            )
        );

        self::assertSame(
            [6, 7, 8, 9],
            $policy->mysticArcanumLevels(
                $this->warlock(17)
            )
        );
    }

    public function testPactSaveDcAndAttackUseCharisma(): void
    {
        $warlock = $this->warlock(5);
        $policy = new WarlockPatronPolicy();

        self::assertSame(
            8
            + $warlock->proficiencyBonus()->value()
            + $warlock->abilityScores()->charisma()->modifier(),
            $policy->pactSaveDc($warlock)
        );

        self::assertSame(
            $warlock->proficiencyBonus()->value()
            + $warlock->abilityScores()->charisma()->modifier(),
            $policy->pactAttackBonus($warlock)
        );
    }

    public function testRegisterShowsAwaitingContractWhenNoPatronChosen(): void
    {
        $register = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $this->warlock(1)
        );

        self::assertSame(
            'Awaiting Patron Contract',
            $register['patron']['label']
        );

        self::assertFalse(
            $register['patron']['chosen']
        );
    }

    public function testRegisterShowsNextMilestone(): void
    {
        $register = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $this->warlock(1)
        );

        self::assertSame(
            2,
            $register[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            'Eldritch Invocations',
            $register[
                'next_milestone'
            ]['label']
        );
    }

    public function testAllFourPatronsHaveDecisionGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'warlock'
            )
        );

        self::assertCount(4, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['identity']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['playstyle']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['best_for']
                        ?? ''
                    )
                )
            );
        }
    }

    public function testControllerAndLedgerReceivePatronRegister(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'WarlockPatronRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'patronRegister' => \$patronRegister",
            $controller
        );

        self::assertStringContainsString(
            'The Patron Contract Register',
            $view
        );

        self::assertStringContainsString(
            'data-patron-register',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-patron-register-title"',
            $view
        );
    }

    public function testPatronRegisterRemainsReadOnlyInThisSlice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-pact-slot-spend',
            $view
        );

        self::assertStringNotContainsString(
            'data-invocation-spend',
            $view
        );
    }

    public function testRegisterPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-patron-register',
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

    public function testPolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new WarlockPatronPolicy())
            ->pactSlots(
                $this->character(
                    'fighter',
                    5
                )
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
                'Patron Register Tester'
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
