<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Vitals;

use PHPUnit\Framework\TestCase;

final class DiceworksVitalMeasuresBridgeTest extends TestCase
{
    public function testDiceworksOffersExplicitSemanticVitalActions(): void
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
        self::assertStringContainsString(
            'data-guild-dice-vitals',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-apply-vitals',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-vitals-status',
            $view
        );
        self::assertStringContainsString(
            "['damage', 'healing'].includes(selection.kind)",
            $dice
        );
        self::assertStringContainsString(
            "prepareVitalAction(selection, total);",
            $dice
        );
        self::assertStringContainsString(
            "'Apply '",
            $dice
        );
        self::assertStringContainsString(
            "'gmrc:vital-apply'",
            $dice
        );
        self::assertStringContainsString(
            'detail: pendingVitalResult',
            $dice
        );
        self::assertStringContainsString(
            'clearVitalAction();',
            $dice
        );
    }

    public function testVitalMeasuresCommitsDiceResultsThroughCharacterDomain(): void
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
            'data-vital-source',
            $view
        );
        self::assertStringContainsString(
            'data-vital-commit-action',
            $view
        );
        self::assertStringContainsString(
            'data-vital-commit-amount',
            $view
        );
        self::assertStringContainsString(
            'data-vital-return-tab',
            $view
        );
        self::assertStringContainsString(
            "'gmrc:vital-apply'",
            $ledger
        );
        self::assertStringContainsString(
            "commitSource.value = 'diceworks';",
            $ledger
        );
        self::assertStringContainsString(
            'form.requestSubmit();',
            $ledger
        );
        self::assertStringContainsString(
            "if (\$source === 'diceworks')",
            $controller
        );
        self::assertStringContainsString(
            'private function applyDiceworksVitalResult',
            $controller
        );
        self::assertStringContainsString(
            "\$character->takeDamage(\$amount);",
            $controller
        );
        self::assertStringContainsString(
            "\$character->heal(\$amount);",
            $controller
        );
        self::assertStringContainsString(
            'Temporary HP %d → %d · Current HP %d → %d.',
            $controller
        );
        self::assertStringContainsString(
            'Applied %d healing. Current HP %d → %d.',
            $controller
        );
    }
}
