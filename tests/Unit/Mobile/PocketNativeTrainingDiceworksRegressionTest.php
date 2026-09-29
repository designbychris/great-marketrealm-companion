<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeTrainingDiceworksRegressionTest extends TestCase
{
    public function testAbilityChecksUseServerResolvedAbilityModifiers(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($api);
        self::assertIsString($client);
        self::assertStringContainsString("'ability_modifiers' => [", $api);
        self::assertStringContainsString("'STR' => \$character->abilityScores()->strength()->modifier()", $api);
        self::assertStringContainsString("'CHA' => \$character->abilityScores()->charisma()->modifier()", $api);
        self::assertStringContainsString('character.ability_modifiers?.[ability]', $client);
        self::assertStringNotContainsString('Math.floor((Number(score)', $client);
    }

    public function testTrainingRollsShareSecureNativeDiceworksWithRollModesAndShortHistory(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('function secureD20()', $client);
        self::assertStringContainsString('crypto.getRandomValues(values)', $client);
        self::assertStringContainsString("diceMode = 'normal'", $client);
        self::assertStringContainsString("mode === 'advantage' ? Math.max(first, second) : Math.min(first, second)", $client);
        self::assertStringContainsString('const DICE_HISTORY_LIMIT = 6;', $client);
        self::assertStringContainsString('function performTrainingRoll(label, modifier', $client);
        self::assertStringContainsString('createDiceworks()', $client);
    }

    public function testNaturalTwentyAndNaturalOneKeepTheEstablishedDiceworksReactions(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString("natural20 ? 'Natural 20!' : 'Natural 1 — Oh dear.'", $client);
        self::assertStringContainsString('const pieces = natural20 ? 28 : 1;', $client);
        self::assertStringContainsString('The Guild has elected not to record that one.', $client);
    }

    public function testDiceworksPersistsAcrossDashboardTabsAndOverviewSpacingGetsFieldPolish(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $css = file_get_contents($root . '/native/pocket-companion/src/native.css');

        self::assertStringContainsString('characterLedger.append(hero, overview, characterPanel, combat, spells, more, diceworks, dock);', $client);
        self::assertStringContainsString('.native-diceworks { position:fixed;', $css);
        self::assertStringContainsString('.native-dashboard-panel > .measure-grid { margin-top:14px; }', $css);
        self::assertStringContainsString('.native-dashboard-panel { padding-bottom:150px; }', $css);
    }
}
