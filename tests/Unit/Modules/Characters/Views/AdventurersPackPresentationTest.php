<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdventurersPackPresentationTest extends TestCase
{
    public function testOpenLedgerContainsEquipmentTabAndPanel(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-ledger-tab="equipment"', $view);
        self::assertStringContainsString('data-ledger-panel="equipment"', $view);
        self::assertStringContainsString('Auby’s Packing Register', $view);
        self::assertStringContainsString('Quartermaster’s Counter', $view);
    }

    public function testInventoryStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents($root . '/app/Providers/FrontendServiceProvider.php');

        self::assertIsString($provider);
        self::assertStringContainsString('gmrc-adventurers-pack', $provider);
        self::assertFileExists($root . '/assets/css/modules/characters/adventurers-pack.css');
    }
}
