<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketArmourClassRegressionTest extends TestCase
{
    public function testPocketProjectsTheLedgersEquipmentAwareArmourClass(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $presenter = file_get_contents($root . '/app/Modules/Characters/Inventory/Services/InventoryPresenter.php');

        self::assertIsString($api);
        self::assertIsString($presenter);
        self::assertStringContainsString('$inventoryPresenter = new InventoryPresenter($catalogue);', $api);
        self::assertStringContainsString('$armourClass = $inventoryPresenter->armourClass($character, $inventory);', $api);
        self::assertStringContainsString("'armour_class' => \$armourClass", $api);
        self::assertStringNotContainsString("'armour_class' => \$character->armourClass()->value()", $api);

        // Guard the three rules that make this projection equipment-aware.
        self::assertStringContainsString('$item->armourBase()', $presenter);
        self::assertStringContainsString('$item->dexterityCap()', $presenter);
        self::assertStringContainsString('$item->armourBonus()', $presenter);
    }
}
