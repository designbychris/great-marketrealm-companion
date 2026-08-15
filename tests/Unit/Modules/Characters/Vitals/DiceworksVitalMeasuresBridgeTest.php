<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Vitals;

use PHPUnit\Framework\TestCase;

final class DiceworksVitalMeasuresBridgeTest extends TestCase
{
    public function testDiceworksDoesNotApplyUntargetedDamageOrHealing(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $dice = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($view);
        self::assertIsString($dice);
        self::assertStringNotContainsString(
            'data-guild-dice-apply-vitals',
            $view
        );
        self::assertStringNotContainsString(
            'data-guild-dice-vitals',
            $view
        );
        self::assertStringNotContainsString(
            "prepareVitalAction(selection, total);",
            $dice
        );
        self::assertStringNotContainsString(
            "'gmrc:vital-apply'",
            $dice
        );
        self::assertStringContainsString(
            "selection.kind === 'healing'",
            $dice
        );
        self::assertStringContainsString(
            "'Damage Roll'",
            $dice
        );
        self::assertStringContainsString(
            "'Healing Roll'",
            $dice
        );
    }

    public function testManualVitalMeasuresRemainIndependentOfDiceTargeting(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $ledger = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );
        $controller = file_get_contents(
            $root . '/app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertIsString($view);
        self::assertIsString($ledger);
        self::assertIsString($controller);
        self::assertStringContainsString(
            'data-vital-measures-form',
            $view
        );
        self::assertStringContainsString(
            'name="current_hp"',
            $view
        );
        self::assertStringContainsString(
            'name="temporary_hp"',
            $view
        );
        self::assertStringNotContainsString(
            'data-vital-source',
            $view
        );
        self::assertStringNotContainsString(
            "'gmrc:vital-apply'",
            $ledger
        );
        self::assertStringContainsString(
            'public function updateVitalMeasures',
            $controller
        );
        self::assertStringContainsString(
            '$character->updateVitalMeasures(',
            $controller
        );
    }
}
