<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class ClashOfTheLedgerPresentationTest extends TestCase
{
    public function testLedgerContainsAttacksTabAndCombatRollContracts(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');
        self::assertIsString($view);
        self::assertStringContainsString('data-ledger-tab="attacks"', $view);
        self::assertStringContainsString('data-ledger-panel="attacks"', $view);
        self::assertStringContainsString('data-guild-roll="damage"', $view);
        self::assertStringContainsString('data-roll-kind="attack"', $view);
    }

    public function testCombatStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        self::assertFileExists($root . '/assets/css/modules/characters/clash-of-the-ledger.css');
        $provider = file_get_contents($root . '/app/Providers/FrontendServiceProvider.php');
        self::assertStringContainsString('gmrc-clash-of-the-ledger', $provider);
    }

    public function testGuildDiceSupportsDamageFormulaRolls(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents($root . '/assets/js/modules/characters/guild-dice.js');
        self::assertStringContainsString('rollFormula', $script);
        self::assertStringContainsString("selection.kind === 'damage'", $script);
        self::assertStringContainsString('Double the weapon dice', $script);
    }
}
