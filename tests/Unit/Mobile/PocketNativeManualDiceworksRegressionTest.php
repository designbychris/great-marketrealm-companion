<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeManualDiceworksRegressionTest extends TestCase
{
    public function testNativeDiceDrawerOffersTheEstablishedGuildDiceSet(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('Dice Drawer', $client);
        self::assertStringContainsString('${[4,6,8,10,12,20,100].map', $client);
        self::assertStringContainsString('data-manual-die', $client);
        self::assertStringContainsString('data-manual-roll', $client);
    }

    public function testManualRollsReuseSecureDiceAndTheSharedSessionHistory(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('function performManualRoll(sides, count, modifier)', $client);
        self::assertStringContainsString('const rolled = rollFormula(`${diceCount}d${die}`, bonus);', $client);
        self::assertStringContainsString("showDiceResult(`Manual \${rolled.formula}`, rolled.total, summary, 'manual', natural);", $client);
        self::assertStringContainsString('crypto.getRandomValues(values)', $client);
        self::assertStringContainsString('const DICE_HISTORY_LIMIT = 6;', $client);
        self::assertStringNotContainsString('Math.random()', $client);
    }

    public function testManualDrawerBoundsCountAndModifierAndKeepsSingleD20NaturalReactions(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $css = file_get_contents($root . '/native/pocket-companion/src/native.css');

        self::assertIsString($client);
        self::assertIsString($css);
        self::assertStringContainsString('diceCount < 1 || diceCount > 20', $client);
        self::assertStringContainsString('bonus < -99 || bonus > 99', $client);
        self::assertStringContainsString('die === 20 && diceCount === 1 ? rolled.dice[0] : null', $client);
        self::assertStringContainsString('const pieces = natural20 ? 28 : 1;', $client);
        self::assertStringContainsString('.dice-types { display:grid; grid-template-columns:repeat(7,1fr);', $css);
        self::assertStringContainsString('.dice-manual-controls { display:grid;', $css);
    }
}
