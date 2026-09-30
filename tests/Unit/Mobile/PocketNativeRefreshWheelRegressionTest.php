<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativeRefreshWheelRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function testRegisterRefreshSpinnerReplacesGlyphWithoutMovingTheButton(): void
    {
        $css = file_get_contents($this->root . '/native/pocket-companion/src/native.css');
        self::assertIsString($css);
        self::assertStringContainsString('#register-refresh[aria-busy="true"] { font-size:0; }', $css);
        self::assertStringContainsString('#register-refresh[aria-busy="true"]::after', $css);
        self::assertStringContainsString('width:18px; height:18px; margin:0;', $css);
    }

    public function testRegisterRefreshStillUsesExistingBusyLifecycle(): void
    {
        $script = file_get_contents($this->root . '/native/pocket-companion/src/main.js');
        self::assertIsString($script);
        self::assertStringContainsString("registerRefresh.setAttribute('aria-busy', 'true')", $script);
        self::assertStringContainsString("registerRefresh.removeAttribute('aria-busy')", $script);
        self::assertStringContainsString('registerRefresh.disabled = true;', $script);
        self::assertStringContainsString('registerRefresh.disabled = false;', $script);
    }
}
