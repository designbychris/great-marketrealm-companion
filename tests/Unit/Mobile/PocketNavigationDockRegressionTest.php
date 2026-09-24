<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNavigationDockRegressionTest extends TestCase
{
    public function testFivePrimaryNavigationItemsAndEquipmentInMore(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("const more=el('button','gmrc-pocket-dashboard__tab gmrc-pocket-dashboard__more','More')", $source);
        self::assertStringContainsString("equipmentButton.remove();moreMenu.append(equipmentButton)", $source);
        self::assertStringContainsString("more.setAttribute('aria-expanded'", $source);
        self::assertStringContainsString("tabs.append(more)", $source);
    }

    public function testDiceTrayAndNavigationHaveSeparateMobileSafeAreaPositions(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("bottomDock.append(dock,moreMenu,tabs)", $source);
        self::assertStringContainsString('gmrc-pocket-bottom-dock{position:fixed', $source);
        self::assertStringContainsString("dock.append(diceTray)", $source);
    }
}
