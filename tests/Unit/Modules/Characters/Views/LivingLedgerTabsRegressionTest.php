<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class LivingLedgerTabsRegressionTest extends TestCase
{
    public function testOpenLedgerProvidesAccessibleBottomTabs(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-living-ledger', $view);
        self::assertStringContainsString('role="tablist"', $view);
        self::assertStringContainsString('Overview', $view);
        self::assertStringContainsString('Skills & Training', $view);
        self::assertStringContainsString('Archive Notes', $view);
        self::assertSame(3, substr_count($view, 'role="tabpanel"'));
    }

    public function testLivingLedgerScriptSupportsKeyboardNavigation(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString("event.key === 'ArrowRight'", $script);
        self::assertStringContainsString("event.key === 'ArrowLeft'", $script);
        self::assertStringContainsString("event.key === 'Home'", $script);
        self::assertStringContainsString("event.key === 'End'", $script);
    }
}
