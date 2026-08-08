<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildNavigationLayoutTest extends TestCase
{
    public function testAppLayoutUsesNavigationTopbar(): void
    {
        $root = dirname(__DIR__, 3);

        $layout = file_get_contents(
            $root
            . '/app/Core/View/Templates/layouts/'
            . 'app.php'
        );

        self::assertIsString($layout);

        self::assertStringContainsString(
            'gmrc-topbar--guild-navigation',
            $layout
        );

        self::assertStringContainsString(
            'guild-navigation.css',
            $layout
        );

        self::assertStringContainsString(
            'guild-navigation.js',
            $layout
        );

        self::assertStringNotContainsString(
            'gmrc-topbar__title',
            $layout
        );
    }

    public function testSidebarComponentIsNowHorizontalNavigation(): void
    {
        $root = dirname(__DIR__, 3);

        $component = file_get_contents(
            $root
            . '/app/Core/View/Templates/components/'
            . 'sidebar.php'
        );

        self::assertIsString($component);

        self::assertStringContainsString(
            'data-guild-navigation',
            $component
        );

        self::assertStringContainsString(
            'Guild Menu',
            $component
        );

        self::assertStringContainsString(
            'aria-controls',
            $component
        );

        self::assertStringNotContainsString(
            '<aside class="gmrc-sidebar">',
            $component
        );
    }
}
