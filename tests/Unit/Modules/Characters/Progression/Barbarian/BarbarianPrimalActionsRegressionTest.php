<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianPrimalActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class BarbarianPrimalActionsRegressionTest extends TestCase
{
    public function testNonBarbarianHasNoPrimalActions(): void
    {
        $state = (
            new BarbarianPrimalActionPresenter()
        )->present(
            $this->character(
                'fighter',
                4
            )
        );

        self::assertFalse(
            $state['supported']
        );

        self::assertSame(
            [],
            $state['actions']
        );
    }

    public function testRageDamageActionReflectsCurrentRageState(): void
    {
        $presenter =
            new BarbarianPrimalActionPresenter();

        $dormant = $presenter->present(
            $this->barbarian(3),
            ActiveClassConditionState::fresh()
        );

        $active = $presenter->present(
            $this->barbarian(3),
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );

        self::assertFalse(
            $dormant['actions'][0]['available']
        );

        self::assertSame(
            'Dormant',
            $dormant['actions'][0]['badge']
        );

        self::assertTrue(
            $active['actions'][0]['available']
        );

        self::assertSame(
            '+2',
            $active['actions'][0]['badge']
        );
    }

    public function testRageDamageActionScalesWithCertifiedLevel(): void
    {
        $active =
            ActiveClassConditionState::fresh()
                ->activate('rage');

        self::assertSame(
            '+2',
            $this->actions(
                8,
                $active
            )[0]['badge']
        );

        self::assertSame(
            '+3',
            $this->actions(
                9,
                $active
            )[0]['badge']
        );

        self::assertSame(
            '+4',
            $this->actions(
                16,
                $active
            )[0]['badge']
        );
    }

    public function testRecklessAttackUnlocksAtLevelTwoWithoutInventingARoll(): void
    {
        $locked = $this->actions(1)[1];
        $unlocked = $this->actions(2)[1];

        self::assertFalse(
            $locked['unlocked']
        );

        self::assertTrue(
            $unlocked['unlocked']
        );

        self::assertNull(
            $unlocked['roll']
        );

        self::assertStringContainsString(
            'select Advantage',
            $unlocked['detail']
        );
    }

    public function testDangerSenseUsesCharactersDexteritySavingThrow(): void
    {
        $character = $this->customBarbarian(2);

        $dangerSense = (
            new BarbarianPrimalActionPresenter()
        )->present(
            $character
        )['actions'][2];

        self::assertSame(
            'saving-throw',
            $dangerSense['roll']['kind']
        );

        self::assertSame(
            'dexterity',
            $dangerSense['roll']['ability']
        );

        self::assertSame(
            $character
                ->savingThrows()
                ->dexterity()
                ->modifier(),
            $dangerSense['roll']['modifier']
        );

        self::assertSame(
            'advantage',
            $dangerSense['roll']['default_mode']
        );
    }

    public function testBrutalCriticalGuidanceScalesAtNineThirteenAndSeventeen(): void
    {
        self::assertSame(
            '+1 die',
            $this->actions(9)[3]['badge']
        );

        self::assertSame(
            '+2 dice',
            $this->actions(13)[3]['badge']
        );

        self::assertSame(
            '+3 dice',
            $this->actions(17)[3]['badge']
        );

        self::assertNull(
            $this->actions(17)[3]['roll']
        );
    }

    public function testRelentlessRageUnlocksAtElevenButRequiresActiveRage(): void
    {
        $dormant = $this->actions(11)[4];

        $active = $this->actions(
            11,
            ActiveClassConditionState::fresh()
                ->activate('rage')
        )[4];

        self::assertTrue(
            $dormant['unlocked']
        );

        self::assertFalse(
            $dormant['available']
        );

        self::assertTrue(
            $active['available']
        );
    }

    public function testRelentlessRageUsesRealConstitutionSaveModifier(): void
    {
        $character = $this->customBarbarian(11);

        $action = (
            new BarbarianPrimalActionPresenter()
        )->present(
            $character,
            ActiveClassConditionState::fresh()
                ->activate('rage')
        )['actions'][4];

        self::assertSame(
            'constitution',
            $action['roll']['ability']
        );

        self::assertSame(
            'proficient',
            $action['roll']['proficiency']
        );

        self::assertSame(
            $character
                ->savingThrows()
                ->constitution()
                ->modifier(),
            $action['roll']['modifier']
        );

        self::assertStringContainsString(
            'first DC 10',
            $action['roll']['result_suffix']
        );
    }

    public function testIndomitableMightUsesStrengthModifierAndScoreFloor(): void
    {
        $character = $this->customBarbarian(18);

        $action = (
            new BarbarianPrimalActionPresenter()
        )->present(
            $character
        )['actions'][5];

        self::assertTrue(
            $action['unlocked']
        );

        self::assertSame(
            $character
                ->abilityScores()
                ->strength()
                ->modifier(),
            $action['roll']['modifier']
        );

        self::assertSame(
            'Minimum 18',
            $action['badge']
        );

        self::assertStringContainsString(
            'minimum 18',
            $action['roll']['result_suffix']
        );
    }

    public function testRageRegisterCarriesPrimalActionContracts(): void
    {
        $state = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->barbarian(11),
            null,
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );

        self::assertCount(
            6,
            $state['actions']
        );

        self::assertSame(
            'rage-damage',
            $state['actions'][0]['key']
        );

        self::assertSame(
            'relentless-rage',
            $state['actions'][4]['key']
        );

        self::assertTrue(
            $state['actions'][4]['available']
        );
    }

    public function testLedgerRendersPrimalActionsAndDiceContracts(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Barbarian Battle Actions',
            $source
        );

        self::assertStringContainsString(
            'gmrc-primal-action-roll',
            $source
        );

        self::assertStringContainsString(
            'data-roll-default-mode=',
            $source
        );

        self::assertStringContainsString(
            'data-roll-modifier=',
            $source
        );

        self::assertStringContainsString(
            'data-roll-result-suffix=',
            $source
        );
    }

    public function testUnavailablePrimalRollsRenderDisabled(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            '! $primalAvailable',
            $source
        );

        self::assertStringContainsString(
            "? 'disabled'",
            $source
        );
    }

    public function testDiceworksSupportsContextualDefaultRollMode(): void
    {
        $source = $this->source(
            'assets/js/modules/characters/'
            . 'guild-dice.js'
        );

        self::assertStringContainsString(
            'dataset.rollDefaultMode',
            $source
        );

        self::assertStringContainsString(
            'selection.defaultMode',
            $source
        );

        self::assertStringContainsString(
            'preferredMode',
            $source
        );
    }

    public function testPrimalActionsRemainResponsiveAndAccessible(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-primal-actions',
            $source
        );

        self::assertStringContainsString(
            '.gmrc-primal-action-roll',
            $source
        );

        self::assertStringContainsString(
            '@media (max-width: 700px)',
            $source
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $source
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function actions(
        int $level,
        ?ActiveClassConditionState $conditions = null
    ): array {
        return (
            new BarbarianPrimalActionPresenter()
        )->present(
            $this->barbarian($level),
            $conditions
        )['actions'];
    }

    private function customBarbarian(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Primal Action Specialist'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'barbarian'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(30),
            AbilityScores::fromScores(
                AbilityScore::fromInt(18),
                AbilityScore::fromInt(14),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(8),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(10)
            )
        );
    }

    private function barbarian(
        int $level
    ): Character {
        return $this->character(
            'barbarian',
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
                'Primal Action Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
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
