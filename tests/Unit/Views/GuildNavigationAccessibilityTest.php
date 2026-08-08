<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildNavigationAccessibilityTest extends TestCase
{
    public function testMobileNavigationSupportsEscapeAndExpandedState(): void
    {
        $root = dirname(__DIR__, 3);

        $script = file_get_contents(
            $root
            . '/assets/js/components/navigation/'
            . 'guild-navigation.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            "event.key !== 'Escape'",
            $script
        );

        self::assertStringContainsString(
            "'aria-expanded'",
            $script
        );

        self::assertStringContainsString(
            'toggle.focus()',
            $script
        );
    }

    public function testActiveNavigationHasGuildGoldTreatment(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/navigation/'
            . 'guild-navigation.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '#402617',
            $css
        );

        self::assertStringContainsString(
            '#d8a84e',
            $css
        );

        self::assertStringContainsString(
            'max-width: 900px',
            $css
        );
    }
}
