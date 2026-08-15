<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceLedgerRegressionTest extends TestCase
{
    public function testLedgerExposesCharacterScopedDiceHistoryControls(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-character-id="<?php echo esc_attr($characterId); ?>"',
            $view
        );
        self::assertStringContainsString(
            'The Dice Ledger',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-history-clear',
            $view
        );
        self::assertStringContainsString(
            'Kept for this adventurer during this browser session.',
            $view
        );
    }

    public function testDiceLedgerPersistsStructuredHistoryForSession(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const MAX_HISTORY = 12;',
            $script
        );
        self::assertStringContainsString(
            "'gmrc:guild-dice:history:' + characterId",
            $script
        );
        self::assertStringContainsString(
            'window.sessionStorage.getItem(historyKey)',
            $script
        );
        self::assertStringContainsString(
            'window.sessionStorage.setItem(',
            $script
        );
        self::assertStringContainsString(
            'JSON.stringify(recent)',
            $script
        );
        self::assertStringContainsString(
            'formula: details.formula',
            $script
        );
        self::assertStringContainsString(
            'dice: Array.isArray(details.dice)',
            $script
        );
        self::assertStringContainsString(
            'natural: Number(details.natural)',
            $script
        );
        self::assertStringContainsString(
            'reaction: details.reaction',
            $script
        );
    }

    public function testDiceLedgerCanBeClearedWithoutBreakingRolling(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const clearHistory = function ()',
            $script
        );
        self::assertStringContainsString(
            'recent.splice(0, recent.length);',
            $script
        );
        self::assertStringContainsString(
            "live.textContent = 'The Dice Ledger has been cleared.';",
            $script
        );
        self::assertStringContainsString(
            "historyClear.addEventListener('click', clearHistory);",
            $script
        );
        self::assertStringContainsString(
            'Dice rolling must remain available if storage is blocked.',
            $script
        );
    }
}
