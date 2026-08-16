<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceSituationalModifiersRegressionTest extends TestCase
{
    public function testDiceworksExposesNextRollSituationalControls(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-guild-situational-panel',
            $view
        );
        self::assertStringContainsString(
            'data-guild-situational-flat',
            $view
        );
        self::assertStringContainsString(
            'data-guild-situational-die',
            $view
        );
        self::assertStringContainsString(
            'data-guild-situational-shortcut="-2"',
            $view
        );
        self::assertStringContainsString(
            'data-guild-situational-shortcut="2"',
            $view
        );
        self::assertStringContainsString(
            'Applied to the next roll only',
            $view
        );
        self::assertStringContainsString(
            'never changes the adventurer’s certified modifier',
            $view
        );
    }

    public function testSituationalMathIsSeparateFromCertifiedModifier(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const situationalAdjustment = function ()',
            $script
        );
        self::assertStringContainsString(
            'const situationalText = function (adjustment)',
            $script
        );
        self::assertStringContainsString(
            'selection.modifier',
            $script
        );
        self::assertStringContainsString(
            '+ adjustment.total',
            $script
        );
        self::assertStringContainsString(
            "'situational ' + signed(adjustment.flat)",
            $script
        );
        self::assertStringContainsString(
            "'situational d'",
            $script
        );
        self::assertStringContainsString(
            'paintSituationalDie(adjustment);',
            $script
        );
        self::assertStringContainsString(
            'situational: adjustment',
            $script
        );
    }

    public function testSituationalAdjustmentResetsAfterSuccessfulRoll(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const resetSituational = function ()',
            $script
        );
        self::assertStringContainsString(
            "situationalFlat.value = '0';",
            $script
        );
        self::assertStringContainsString(
            "situationalDie.value = '0';",
            $script
        );
        self::assertGreaterThanOrEqual(
            3,
            substr_count($script, 'resetSituational();')
        );
        self::assertStringContainsString(
            "situationalPanel.open = false;",
            $script
        );
    }

    public function testBonusDieCannotChangeNaturalD20Reaction(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'naturalReaction(rolled.natural)',
            $script
        );
        self::assertStringNotContainsString(
            'naturalReaction(total)',
            $script
        );
        self::assertStringContainsString(
            'natural: rolled.natural',
            $script
        );
        self::assertStringContainsString(
            'dieValue: dieValue',
            $script
        );
    }
}
