<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Inventory;

use PHPUnit\Framework\TestCase;

final class InventoryRouteContractTest extends TestCase
{
    public function testCharacterRoutesExposeInventoryLifecycle(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents($root . '/app/Modules/Characters/Routes.php');

        self::assertIsString($routes);
        self::assertStringContainsString("'/characters/{id}/inventory'", $routes);
        self::assertStringContainsString("'/characters/{id}/inventory/{item}'", $routes);
        self::assertStringContainsString("'/characters/{id}/inventory/{item}/equip'", $routes);
    }
}
