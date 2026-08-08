<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildNavigationIconRenderingTest extends TestCase
{
    public function testNavigationUsesExplicitSvgWhitelist(): void
    {
        $root = dirname(__DIR__, 3);

        $component = file_get_contents(
            $root
            . '/app/Core/View/Templates/components/'
            . 'sidebar.php'
        );

        self::assertIsString($component);

        self::assertStringContainsString(
            '$navigationIconHtml',
            $component
        );

        self::assertStringContainsString(
            "'svg' => [",
            $component
        );

        self::assertStringContainsString(
            "'path' => [",
            $component
        );

        self::assertStringNotContainsString(
            "wp_kses_post(\n                            \$item['icon']",
            $component
        );
    }

    public function testNavigationIconsRemainSvgAssets(): void
    {
        $root = dirname(__DIR__, 3);

        $icons = file_get_contents(
            $root
            . '/app/Navigation/Icons.php'
        );

        self::assertIsString($icons);

        self::assertStringContainsString(
            '<svg viewBox="0 0 24 24"',
            $icons
        );

        self::assertStringContainsString(
            'fill="currentColor"',
            $icons
        );
    }
}
