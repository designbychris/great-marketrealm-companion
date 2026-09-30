<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativeGateViewportRegressionTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 3);
    }

    public function testColdLaunchDeclaresGateLayoutBeforeJavascriptRestoresSession(): void
    {
        $html = file_get_contents($this->root() . '/native/pocket-companion/index.html');

        self::assertIsString($html);
        self::assertStringContainsString('<main class="native-shell" data-view="gate">', $html);
    }

    public function testRestoredSessionExplicitlyReappliesGateLayout(): void
    {
        $javascript = file_get_contents($this->root() . '/native/pocket-companion/src/main.js');

        self::assertIsString($javascript);
        self::assertMatchesRegularExpression('/const showSignedIn = session => \\{.*?showView\\(\'gate\'\\);/s', $javascript);
    }

    public function testGateSceneOwnsTheFullViewportWhileContentKeepsSafeAreaPadding(): void
    {
        $css = file_get_contents($this->root() . '/native/pocket-companion/src/native.css');

        self::assertIsString($css);
        self::assertStringContainsString('.native-shell[data-view="gate"] { padding:0; }', $css);
        self::assertStringContainsString('.gate-scene { width:100vw; max-width:none; min-height:100vh; min-height:100dvh;', $css);
        self::assertStringContainsString('env(safe-area-inset-top)', $css);
        self::assertStringContainsString('env(safe-area-inset-bottom)', $css);
    }
}
