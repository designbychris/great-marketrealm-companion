<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceTargetingContractRegressionTest extends TestCase
{
    public function testLedgerExposesTargetKindsWithoutApplyingVitality(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-guild-targeting',
            $view
        );
        self::assertStringContainsString(
            'data-guild-target-kind',
            $view
        );
        self::assertStringContainsString(
            'data-guild-target-name',
            $view
        );
        self::assertStringContainsString(
            'No HP changes in this phase.',
            $view
        );
        self::assertStringContainsString(
            'data-roll-target-mode=',
            $view
        );
        self::assertStringContainsString(
            'data-roll-default-target-kind=',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-target-result',
            $view
        );
        self::assertStringNotContainsString(
            'data-guild-dice-apply-vitals',
            $view
        );
    }

    public function testDiceworksCarriesStructuredTargetThroughRollHistory(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const selectedTarget = function (selection)',
            $script
        );
        self::assertStringContainsString(
            "selection.targetMode !== 'creature'",
            $script
        );
        self::assertStringContainsString(
            "resolved:",
            $script
        );
        self::assertStringContainsString(
            'target: details.target',
            $script
        );
        self::assertStringContainsString(
            'targetSuffix(target)',
            $script
        );
        self::assertStringContainsString(
            'paintTargetResult(target);',
            $script
        );
        self::assertStringContainsString(
            'target: target',
            $script
        );
        self::assertStringNotContainsString(
            "'gmrc:vital-apply'",
            $script
        );
    }

    public function testCriticalDamageInheritsOriginalAttackTarget(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const prepareCriticalFollowUp = function (selection, target)',
            $script
        );
        self::assertStringContainsString(
            'target: target',
            $script
        );
        self::assertStringContainsString(
            'paintTargetResult(critical.target || null);',
            $script
        );
        self::assertStringContainsString(
            'targetSuffix(critical.target || null)',
            $script
        );
        self::assertStringContainsString(
            'target: critical.target || null',
            $script
        );
    }
}
