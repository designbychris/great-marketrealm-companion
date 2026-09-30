<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativePolishRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function testRegisterRefreshHasBusyGuardAndKeepsExistingCardsDuringRefresh(): void
    {
        $script = file_get_contents($this->root . '/native/pocket-companion/src/main.js');
        self::assertIsString($script);
        self::assertStringContainsString('if (registerLoading) return;', $script);
        self::assertStringContainsString("registerRefresh.setAttribute('aria-busy', 'true')", $script);
        self::assertStringContainsString('if (!liveCharacters.length) characterList.replaceChildren();', $script);
        self::assertStringContainsString('registerRefresh.removeAttribute', $script);
    }

    public function testRegisterUsesPocketNetworkLanguageAndPortraitFallback(): void
    {
        $script = file_get_contents($this->root . '/native/pocket-companion/src/main.js');
        self::assertIsString($script);
        self::assertStringContainsString("liveActionError(error, 'The Adventurers\\' Register')", $script);
        self::assertStringContainsString('data-portrait-image', $script);
        self::assertStringContainsString("addEventListener('error'", $script);
        self::assertStringContainsString("textContent: '✦'", $script);
    }

    public function testSmallPhoneAndTabletLayoutsAreExplicitlyProtected(): void
    {
        $css = file_get_contents($this->root . '/native/pocket-companion/src/native.css');
        self::assertIsString($css);
        self::assertStringContainsString('@media (max-width:340px)', $css);
        self::assertStringContainsString('@media (min-width:700px)', $css);
        self::assertStringContainsString('grid-template-columns:repeat(2,minmax(0,1fr))', $css);
        self::assertStringContainsString('overflow-wrap:anywhere', $css);
    }

    public function testPolishRespectsTouchKeyboardAndReducedMotion(): void
    {
        $css = file_get_contents($this->root . '/native/pocket-companion/src/native.css');
        self::assertIsString($css);
        self::assertStringContainsString('input{font-size:16px;}', $css);
        self::assertStringContainsString('button:disabled', $css);
        self::assertStringContainsString('prefers-reduced-motion:reduce', $css);
    }
}
