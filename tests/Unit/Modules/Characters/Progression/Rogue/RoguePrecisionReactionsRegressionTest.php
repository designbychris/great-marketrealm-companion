<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RoguePrecisionReactionPresenter;
use PHPUnit\Framework\TestCase;

final class RoguePrecisionReactionsRegressionTest extends TestCase
{
    public function testNonRogueIsUnsupported(): void
    {
        $state = (
            new RoguePrecisionReactionPresenter()
        )->present(
            $this->character('fighter', 5)
        );

        self::assertFalse($state['supported']);
    }

    public function testSneakAttackScalesWithRogueLevel(): void
    {
        $presenter =
            new RoguePrecisionReactionPresenter();

        $expected = [
            1 => '1d6',
            3 => '2d6',
            5 => '3d6',
            9 => '5d6',
            13 => '7d6',
            17 => '9d6',
            20 => '10d6',
        ];

        foreach ($expected as $level => $dice) {
            $state = $presenter->present(
                $this->rogue($level)
            );

            self::assertSame(
                $dice,
                $state['sneak_attack']['dice']
            );

            self::assertSame(
                $dice,
                $state[
                    'sneak_attack'
                ]['damage_roll']['formula']
            );
        }
    }

    public function testSneakAttackIsOncePerTurnNotFiniteReserve(): void
    {
        $state = (
            new RoguePrecisionReactionPresenter()
        )->present(
            $this->rogue(5)
        )['sneak_attack'];

        self::assertSame(
            'Once per turn',
            $state['frequency']
        );

        self::assertArrayNotHasKey(
            'remaining',
            $state
        );

        self::assertArrayNotHasKey(
            'uses',
            $state
        );
    }

    public function testSneakAttackUsesSharedDamageRollContract(): void
    {
        $roll = (
            new RoguePrecisionReactionPresenter()
        )->present(
            $this->rogue(7)
        )['sneak_attack']['damage_roll'];

        self::assertSame('damage', $roll['kind']);
        self::assertSame('4d6', $roll['formula']);
        self::assertSame(0, $roll['modifier']);
        self::assertSame(
            'Sneak Attack damage',
            $roll['result_suffix']
        );
    }

    public function testSneakAttackKeepsQualificationAtTheTable(): void
    {
        $state = (
            new RoguePrecisionReactionPresenter()
        )->present(
            $this->rogue(3)
        )['sneak_attack'];

        self::assertCount(
            3,
            $state['qualification']
        );

        self::assertStringContainsString(
            'does not decide',
            $state['qualification'][1]
        );
    }

    public function testUncannyDodgeUnlocksAtLevelFive(): void
    {
        $presenter =
            new RoguePrecisionReactionPresenter();

        self::assertFalse(
            $presenter->present(
                $this->rogue(4)
            )['uncanny_dodge']['unlocked']
        );

        self::assertTrue(
            $presenter->present(
                $this->rogue(5)
            )['uncanny_dodge']['unlocked']
        );

        self::assertSame(
            'Reaction',
            $presenter->present(
                $this->rogue(5)
            )['uncanny_dodge']['frequency']
        );
    }

    public function testEvasionUnlocksAtSevenAndIsPassive(): void
    {
        $presenter =
            new RoguePrecisionReactionPresenter();

        self::assertFalse(
            $presenter->present(
                $this->rogue(6)
            )['evasion']['unlocked']
        );

        $evasion = $presenter->present(
            $this->rogue(7)
        )['evasion'];

        self::assertTrue($evasion['unlocked']);

        self::assertStringContainsString(
            'passive',
            strtolower($evasion['summary'])
        );
    }

    public function testCunningRegisterCarriesPrecisionContracts(): void
    {
        $state = (
            new RogueCunningRegisterPresenter()
        )->present(
            $this->rogue(7)
        );

        self::assertTrue(
            $state[
                'precision_reactions'
            ]['supported']
        );

        self::assertSame(
            '4d6',
            $state['sneak_attack']['dice']
        );

        self::assertTrue(
            $state[
                'precision_reactions'
            ]['uncanny_dodge']['unlocked']
        );
    }

    public function testLedgerRendersPrecisionAndReactionControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Rogue Turn Record',
            $view
        );

        self::assertStringContainsString(
            'data-sneak-attack-roll',
            $view
        );

        self::assertStringContainsString(
            'data-sneak-attack-used',
            $view
        );

        self::assertStringContainsString(
            'data-uncanny-dodge-used',
            $view
        );

        self::assertStringContainsString(
            'data-rogue-new-turn',
            $view
        );
    }

    public function testSneakAttackReusesGuildDiceworksDamageContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'gmrc-guild-roll-trigger',
            $view
        );

        self::assertStringContainsString(
            'data-guild-roll="damage"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula=',
            $view
        );
    }

    public function testTurnScriptResetsSneakAttackAndReactionLocally(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/'
            . 'rogue-precision-reactions.js'
        );

        self::assertStringContainsString(
            '[data-rogue-new-turn]',
            $script
        );

        self::assertStringContainsString(
            'setSneakUsed(false)',
            $script
        );

        self::assertStringContainsString(
            "uncanny.disabled = false",
            $script
        );

        self::assertStringNotContainsString(
            'fetch(',
            $script
        );

        self::assertStringNotContainsString(
            'localStorage',
            $script
        );
    }

    public function testPrecisionScriptIsEnqueuedAfterDiceworks(): void
    {
        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            "'gmrc-rogue-precision-reactions'",
            $provider
        );

        self::assertStringContainsString(
            "'rogue-precision-reactions.js'",
            $provider
        );

        self::assertStringContainsString(
            "['gmrc-guild-dice']",
            $provider
        );
    }

    public function testPrecisionPanelIsResponsiveAndAccessible(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-rogue-precision',
            $css
        );

        self::assertStringContainsString(
            ':focus-visible',
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

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'aria-live="polite"',
            $view
        );
    }

    private function rogue(int $level): Character
    {
        return $this->character('rogue', $level);
    }

    private function character(
        string $class,
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Precision Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
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
