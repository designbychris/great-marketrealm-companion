<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceCriticalDamageRegressionTest extends TestCase
{
    public function testAttackTriggerCarriesPhpResolvedCriticalDamageContext(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $presenter = file_get_contents(
            $root
            . '/app/Modules/Characters/Combat/Services/'
            . 'AttackPresenter.php'
        );

        self::assertIsString($view);
        self::assertIsString($presenter);
        self::assertStringContainsString(
            "'critical_damage_die' => \$this->criticalDamageFormula(",
            $presenter
        );
        self::assertStringContainsString(
            'private function criticalDamageFormula',
            $presenter
        );
        self::assertStringContainsString(
            '((int) $matches[1] * 2)',
            $presenter
        );
        self::assertStringContainsString(
            'data-roll-critical-formula=',
            $view
        );
        self::assertStringContainsString(
            'data-roll-critical-modifier=',
            $view
        );
        self::assertStringContainsString(
            'data-roll-critical-damage-type=',
            $view
        );
    }

    public function testNaturalTwentyAttackPreparesActionableCriticalDamage(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString(
            'data-guild-critical-follow-up',
            $view
        );
        self::assertStringContainsString(
            'data-guild-critical-damage',
            $view
        );
        self::assertStringContainsString(
            'Double the weapon dice; keep the flat modifier once.',
            $view
        );
        self::assertStringContainsString(
            'const prepareCriticalFollowUp = function (selection)',
            $script
        );
        self::assertStringContainsString(
            "selection.kind !== 'attack'",
            $script
        );
        self::assertStringContainsString(
            "if (rolled.natural === 20)",
            $script
        );
        self::assertStringContainsString(
            'prepareCriticalFollowUp(selection);',
            $script
        );
        self::assertStringContainsString(
            "'“Critical hit! Double the weapon dice!” — Auby'",
            $script
        );
    }

    public function testCriticalDamageRollKeepsFlatModifierSingleAndRecordsHistory(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const performCriticalDamage = function ()',
            $script
        );
        self::assertStringContainsString(
            'rollFormula(critical.formula, false)',
            $script
        );
        self::assertStringContainsString(
            '+ critical.modifier',
            $script
        );
        self::assertStringContainsString(
            "kind: 'critical-damage'",
            $script
        );
        self::assertStringContainsString(
            "reaction: 'critical-damage'",
            $script
        );
        self::assertStringContainsString(
            'situational: adjustment',
            $script
        );
        self::assertStringContainsString(
            'clearCriticalFollowUp();',
            $script
        );
        self::assertStringNotContainsString(
            'rollFormula(critical.formula, true)',
            $script
        );
    }

    public function testCriticalWorkflowDoesNotApplyTargetHitPoints(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($script);
        self::assertIsString($view);
        self::assertStringNotContainsString(
            'data-guild-critical-apply-damage',
            $view
        );
        self::assertStringNotContainsString(
            "'gmrc:vital-apply'",
            $script
        );
        self::assertStringNotContainsString(
            'pendingVitalResult',
            $script
        );
    }
}
