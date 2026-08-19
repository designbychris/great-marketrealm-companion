<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class RogueCunningActionsRegressionTest extends TestCase
{
    public function testNonRogueHasNoCunningActions(): void
    {
        $state = (
            new RogueCunningActionPresenter()
        )->present(
            $this->character(
                'fighter',
                4
            )
        );

        self::assertFalse(
            $state['supported']
        );

        self::assertFalse(
            $state['unlocked']
        );

        self::assertSame(
            [],
            $state['actions']
        );
    }

    public function testLevelOneRogueSeesLockedCunningActions(): void
    {
        $state = (
            new RogueCunningActionPresenter()
        )->present(
            $this->rogue(1)
        );

        self::assertTrue(
            $state['supported']
        );

        self::assertFalse(
            $state['unlocked']
        );

        self::assertSame(
            [
                false,
                false,
                false,
            ],
            array_column(
                $state['actions'],
                'unlocked'
            )
        );
    }

    public function testLevelTwoUnlocksDashDisengageAndHide(): void
    {
        $state = (
            new RogueCunningActionPresenter()
        )->present(
            $this->rogue(2)
        );

        self::assertTrue(
            $state['unlocked']
        );

        self::assertSame(
            [
                'dash',
                'disengage',
                'hide',
            ],
            array_column(
                $state['actions'],
                'key'
            )
        );

        self::assertSame(
            [
                true,
                true,
                true,
            ],
            array_column(
                $state['actions'],
                'unlocked'
            )
        );
    }

    public function testCunningActionIsEveryTurnRatherThanFiniteResource(): void
    {
        $state = (
            new RogueCunningActionPresenter()
        )->present(
            $this->rogue(2)
        );

        self::assertSame(
            'Bonus action',
            $state['cost']
        );

        self::assertSame(
            'Every turn',
            $state['refresh']
        );

        foreach ($state['actions'] as $action) {
            self::assertArrayNotHasKey(
                'uses',
                $action
            );

            self::assertArrayNotHasKey(
                'remaining',
                $action
            );
        }
    }

    public function testDashAndDisengageDoNotInventDiceRolls(): void
    {
        $actions = (
            new RogueCunningActionPresenter()
        )->present(
            $this->rogue(2)
        )['actions'];

        self::assertSame(
            'declaration',
            $actions[0]['kind']
        );

        self::assertNull(
            $actions[0]['roll']
        );

        self::assertSame(
            'declaration',
            $actions[1]['kind']
        );

        self::assertNull(
            $actions[1]['roll']
        );
    }

    public function testHideUsesCharactersRealStealthModifier(): void
    {
        $rogue = $this->rogue(
            3,
            'market-runner'
        );

        $hide = (
            new RogueCunningActionPresenter()
        )->present($rogue)[
            'actions'
        ][2];

        self::assertSame(
            'ability-check',
            $hide['kind']
        );

        self::assertSame(
            'dexterity',
            $hide['roll']['ability']
        );

        self::assertSame(
            $rogue
                ->skills()
                ->stealth()
                ->modifier(),
            $hide['roll']['modifier']
        );

        self::assertSame(
            'Dexterity (Stealth) check',
            $hide['roll']['result_suffix']
        );
    }

    public function testHideCarriesRealStealthProficiencyState(): void
    {
        $rogue = $this->rogue(
            3,
            'criminal'
        );

        $stealth = $rogue
            ->skills()
            ->stealth();

        $hide = (
            new RogueCunningActionPresenter()
        )->present($rogue)[
            'actions'
        ][2];

        $expected = $stealth->hasExpertise()
            ? 'expertise'
            : (
                $stealth->isProficient()
                    ? 'proficient'
                    : 'none'
            );

        self::assertSame(
            $expected,
            $hide['roll']['proficiency']
        );
    }

    public function testCunningRegisterCarriesActionContracts(): void
    {
        $state = (
            new RogueCunningRegisterPresenter()
        )->present(
            $this->rogue(2)
        );

        self::assertSame(
            'Bonus action',
            $state['cunning_action']['cost']
        );

        self::assertSame(
            'Every turn',
            $state['cunning_action']['refresh']
        );

        self::assertSame(
            [
                'dash',
                'disengage',
                'hide',
            ],
            array_column(
                $state[
                    'cunning_action'
                ]['actions'],
                'key'
            )
        );
    }

    public function testLedgerRendersThreeCunningActionControls(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Cunning Actions',
            $source
        );

        self::assertStringContainsString(
            'data-cunning-actions',
            $source
        );

        self::assertStringContainsString(
            'data-cunning-declare=',
            $source
        );

        self::assertStringContainsString(
            'Roll Hide',
            $source
        );

        self::assertStringContainsString(
            'data-cunning-status',
            $source
        );
    }

    public function testHideReusesGuildDiceworksContract(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'gmrc-guild-roll-trigger',
            $source
        );

        self::assertStringContainsString(
            'data-roll-modifier=',
            $source
        );

        self::assertStringContainsString(
            'data-roll-proficiency=',
            $source
        );

        self::assertStringContainsString(
            'data-roll-result-suffix=',
            $source
        );
    }

    public function testDeclarationScriptDoesNotPersistFiniteState(): void
    {
        $source = $this->source(
            'assets/js/modules/characters/'
            . 'rogue-cunning-actions.js'
        );

        self::assertStringContainsString(
            '[data-cunning-declare]',
            $source
        );

        self::assertStringContainsString(
            "setAttribute('aria-pressed', 'true')",
            $source
        );

        self::assertStringContainsString(
            'No limited resource has been spent.',
            $source
        );

        self::assertStringNotContainsString(
            'fetch(',
            $source
        );

        self::assertStringNotContainsString(
            'localStorage',
            $source
        );
    }

    public function testCunningActionScriptIsEnqueuedAfterDiceworks(): void
    {
        $source = $this->source(
            'app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            "'gmrc-rogue-cunning-actions'",
            $source
        );

        self::assertStringContainsString(
            "'rogue-cunning-actions.js'",
            $source
        );

        self::assertStringContainsString(
            "['gmrc-guild-dice']",
            $source
        );
    }

    public function testCunningActionsRemainResponsiveAndAccessible(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-cunning-actions',
            $source
        );

        self::assertStringContainsString(
            '.gmrc-cunning-action-button:focus-visible',
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

    public function testPrecisionAndReactionStateRemainDeferred(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Contextual attack handling arrives in',
            $view
        );

        self::assertStringNotContainsString(
            'data-sneak-attack-spent',
            $view
        );

        $script = $this->source(
            'assets/js/modules/characters/'
            . 'rogue-cunning-actions.js'
        );

        self::assertStringNotContainsString(
            'uncanny-dodge',
            $script
        );
    }

    private function rogue(
        int $level,
        string $background = 'market-runner'
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Cunning Action Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'rogue'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            background:
                Background::fromString(
                    $background
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
                'Cunning Isolation Tester'
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
