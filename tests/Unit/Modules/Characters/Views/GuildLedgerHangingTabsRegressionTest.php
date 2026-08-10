<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildLedgerHangingTabsRegressionTest extends TestCase
{
    public function testTabsHaveIconAndLabelStructure(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertSame(6, substr_count($view, 'gmrc-ledger-tab__icon'));
        self::assertSame(6, substr_count($view, 'gmrc-ledger-tab__label'));
        self::assertStringContainsString('Spells & Abilities', $view);
    }

    public function testGuildDiceStylesAnchorTabsToRightPage(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root . '/assets/css/modules/characters/guild-dice.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString('width: calc(50% - 1rem);', $css);
        self::assertStringContainsString('background-color: #603773;', $css);
        self::assertStringContainsString('.gmrc-ledger-tab__icon', $css);
    }
}
