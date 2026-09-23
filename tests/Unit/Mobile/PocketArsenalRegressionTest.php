<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketArsenalRegressionTest extends TestCase
{
    public function testArsenalUsesOwnerScopedInventoryAndCanonicalPresenters(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString('(new CharacterInventoryRepository())->find($character->id())', $source);
        self::assertStringContainsString('(new AttackPresenter($catalogue))->present($character, $inventory)', $source);
        self::assertStringContainsString("'attacks' => \$attacks", $source);
        self::assertStringContainsString("'equipment' => \$inventoryRows", $source);
    }

    public function testArsenalSharesDiceworksAndEquipmentIsReadOnly(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('tray.rollDamage=(label,formula,mod,type)=>', $source);
        self::assertStringContainsString("diceTray.rollCheck(String(attack.label||'Weapon')+' attack',bonus)", $source);
        self::assertStringContainsString("['Critical',attack.critical_damage_die]", $source);
        self::assertStringContainsString("'Equipment · read only'", $source);
        self::assertStringContainsString('pocketArsenal(character,diceTray)', $source);
    }
}
