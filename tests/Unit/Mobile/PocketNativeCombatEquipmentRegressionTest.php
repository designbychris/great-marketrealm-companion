<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeCombatEquipmentRegressionTest extends TestCase
{
    public function testCombatTabUsesAuthoritativeAttackProjection(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($api);
        self::assertIsString($client);
        self::assertStringContainsString('(new AttackPresenter($catalogue))->present($character, $inventory)', $api);
        self::assertStringContainsString("'attacks' => \$attacks", $api);
        self::assertStringContainsString('function combatPanel(character)', $client);
        self::assertStringContainsString('Array.isArray(character.attacks)', $client);
        self::assertStringContainsString('attack.attack_bonus', $client);
        self::assertStringContainsString('attack.damage_die', $client);
        self::assertStringContainsString('attack?.critical_damage_die', $client);
    }

    public function testWeaponAttackDamageAndCriticalDamageUseSharedSecureDiceworks(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('function secureDie(sides)', $client);
        self::assertStringContainsString('crypto.getRandomValues(values)', $client);
        self::assertStringContainsString('function rollFormula(formula, modifier = 0)', $client);
        self::assertStringContainsString('function performAttackRoll(attack)', $client);
        self::assertStringContainsString('function performDamageRoll(attack, critical = false)', $client);
        self::assertStringContainsString("critical ? attack?.critical_damage_die : attack?.damage_die", $client);
        self::assertStringContainsString("performAttackRoll(attack)", $client);
        self::assertStringContainsString("performDamageRoll(attack, button.dataset.combatRoll === 'critical')", $client);
    }

    public function testAttackRollModesAndNaturalResultsRemainOwnedByTheSharedDiceworks(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString('const rolled = rollD20Mode(diceMode);', $client);
        self::assertStringContainsString('showDiceResult(`${attack.label} — Attack`', $client);
        self::assertStringContainsString("natural === 20 ? '. Natural 20. Critical hit.'", $client);
        self::assertStringContainsString("natural === 1 ? '. Natural 1. Auby says: The Guild has elected not to record that one.'", $client);
        self::assertStringContainsString('const pieces = natural20 ? 28 : 1;', $client);
    }

    public function testMoreTabShowsReadOnlyAuthoritativeEquipment(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString("'equipment' => \$inventoryRows", $api);
        self::assertStringContainsString('function equipmentPanel(character)', $client);
        self::assertStringContainsString('Array.isArray(character.equipment)', $client);
        self::assertStringContainsString('Your pack is read-only in the native Pocket.', $client);
        self::assertStringNotContainsString('data-equipment-save', $client);
    }
}
