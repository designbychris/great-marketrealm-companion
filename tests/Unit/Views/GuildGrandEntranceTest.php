<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildGrandEntranceTest extends TestCase
{
    public function testGuildNavigationHasPhysicalDropAnimation(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/navigation/'
            . 'guild-navigation.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '@keyframes gmrc-grand-entrance',
            $css
        );

        self::assertStringContainsString(
            '760ms',
            $css
        );

        self::assertStringContainsString(
            'translateY(-118%)',
            $css
        );

        self::assertStringContainsString(
            'translateY(8px)',
            $css
        );

        self::assertStringContainsString(
            'translateY(-6px)',
            $css
        );
    }

    public function testGrandEntranceRespectsReducedMotion(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/navigation/'
            . 'guild-navigation.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );

        self::assertStringContainsString(
            'animation: none !important',
            $css
        );
    }
}
