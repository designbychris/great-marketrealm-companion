<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class FighterMartialActionsRegressionTest extends TestCase
{
    public function testNonFighterHasNoMartialActions(): void
    {
        $actions = (
            new FighterMartialActionPresenter()
        )->present(
            $this->character(
                'wizard',
                4
            )
        );

        self::assertFalse(
            $actions['supported']
        );

        self::assertSame(
            [],
            $actions['resources']
        );
    }

    public function testSecondWindUsesGuildDiceCompatibleHealingFormula(): void
    {
        $actions = $this->actions(4);
        $secondWind = $actions[
            'resources'
        ]['second-wind'];

        self::assertSame(
            'healing',
            $secondWind['roll']['kind']
        );

        self::assertSame(
            '1d10',
            $secondWind['roll']['formula']
        );

        self::assertSame(
            4,
            $secondWind['roll']['modifier']
        );

        self::assertSame(
            'HP recovered',
            $secondWind['roll']['result_suffix']
        );
    }

    public function testSecondWindModifierTracksCertifiedFighterLevel(): void
    {
        self::assertSame(
            1,
            $this->actions(1)[
                'resources'
            ]['second-wind']['roll']['modifier']
        );

        self::assertSame(
            17,
            $this->actions(17)[
                'resources'
            ]['second-wind']['roll']['modifier']
        );
    }

    public function testActionSurgeIsDeliberatelyNotADiceAction(): void
    {
        $actionSurge = $this->actions(4)[
            'resources'
        ]['action-surge'];

        self::assertNull(
            $actionSurge['roll']
        );

        self::assertSame(
            'Use Action Surge',
            $actionSurge['button_label']
        );

        self::assertStringContainsString(
            'additional action',
            $actionSurge['note']
        );
    }

    public function testIndomitableBuildsAllSixSavingThrowRerolls(): void
    {
        $indomitable = $this->actions(9)[
            'resources'
        ]['indomitable'];

        self::assertCount(
            6,
            $indomitable['save_rerolls']
        );

        self::assertSame(
            [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ],
            array_column(
                $indomitable['save_rerolls'],
                'ability'
            )
        );
    }

    public function testIndomitableUsesCharactersRealSavingThrowModifiers(): void
    {
        $fighter = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Saving Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'fighter'
            ),
            Level::fromInt(9),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::fromScores(
                AbilityScore::fromInt(18),
                AbilityScore::fromInt(14),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(8)
            )
        );

        $actions = (
            new FighterMartialActionPresenter()
        )->present($fighter);

        $rerolls = $actions[
            'resources'
        ]['indomitable']['save_rerolls'];

        $strength = $rerolls[0];
        $dexterity = $rerolls[1];

        self::assertSame(
            $fighter
                ->savingThrows()
                ->strength()
                ->modifier(),
            $strength['modifier']
        );

        self::assertSame(
            $fighter
                ->savingThrows()
                ->dexterity()
                ->modifier(),
            $dexterity['modifier']
        );

        self::assertTrue(
            $strength['proficient']
        );

        self::assertFalse(
            $dexterity['proficient']
        );
    }

    public function testDiceBackedActionsUseMarkSpentLabels(): void
    {
        $actions = $this->actions(9);

        self::assertSame(
            'Mark Second Wind Spent',
            $actions[
                'resources'
            ]['second-wind']['button_label']
        );

        self::assertSame(
            'Mark Indomitable Spent',
            $actions[
                'resources'
            ]['indomitable']['button_label']
        );
    }

    public function testMartialRegisterCarriesActionContractsIntoResources(): void
    {
        $register = (
            new FighterMartialRegisterPresenter()
        )->present(
            $this->fighter(9),
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            '1d10',
            $register[
                'resources'
            ][0]['action']['roll']['formula']
        );

        self::assertSame(
            'Use Action Surge',
            $register[
                'resources'
            ][1]['action']['button_label']
        );

        self::assertCount(
            6,
            $register[
                'resources'
            ][2]['action']['save_rerolls']
        );
    }

    public function testSecondWindRollButtonUsesGuildDiceworksContract(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'gmrc-martial-action-roll',
            $view
        );

        self::assertStringContainsString(
            'data-guild-roll=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-result-suffix=',
            $view
        );
    }

    public function testIndomitableRerollsUseSavingThrowDiceContract(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'gmrc-martial-save-rerolls',
            $view
        );

        self::assertStringContainsString(
            'data-roll-kind="saving-throw"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-source="Indomitable"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-modifier=',
            $view
        );
    }

    public function testMartialActionDiceDisableWhenReserveIsEmpty(): void
    {
        $view = $this->view();

        self::assertGreaterThanOrEqual(
            2,
            substr_count(
                $view,
                "['remaining']"
            )
        );

        self::assertStringContainsString(
            "? 'disabled'",
            $view
        );
    }

    public function testSpendButtonUsesActionSpecificLabel(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            "'button_label'",
            $view
        );

        self::assertStringContainsString(
            "'Spend 1 Use'",
            $view
        );

        self::assertStringContainsString(
            "'Reserve Spent'",
            $view
        );
    }

    public function testMartialActionPresentationIsResponsiveAndAccessible(): void
    {
        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '.gmrc-martial-action-roll',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-martial-save-rerolls',
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

    /**
     * @return array<string,mixed>
     */
    private function actions(
        int $level
    ): array {
        return (
            new FighterMartialActionPresenter()
        )->present(
            $this->fighter($level)
        );
    }

    private function fighter(
        int $level
    ): Character {
        return $this->character(
            'fighter',
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
                'Martial Action Tester'
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

    private function view(): string
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/'
            . 'show.php'
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
