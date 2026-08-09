<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildGardenFooterTest extends TestCase
{
    public function testCompanionDoesNotRenderItsOwnSiteFooter(): void
    {
        $root = dirname(__DIR__, 3);

        $layout = file_get_contents(
            $root
            . '/app/Core/View/Templates/layouts/'
            . 'app.php'
        );

        self::assertIsString($layout);

        self::assertStringNotContainsString(
            'gmrc-guild-footer',
            $layout
        );

        self::assertStringNotContainsString(
            'Where adventure meets ingredients.',
            $layout
        );
    }

    public function testNavigationUsesDedicatedCocoaWoodgrainAsset(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/assets/images/guild-hall/navigation/'
            . 'cocoa-woodgrain.webp'
        );

        self::assertFileExists(
            $root
            . '/art/IllustrationKit/GuildHall/Navigation/'
            . 'cocoa-woodgrain-master.png'
        );

        $css = file_get_contents(
            $root
            . '/assets/css/components/navigation/'
            . 'guild-navigation.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            'cocoa-woodgrain.webp',
            $css
        );

        self::assertStringNotContainsString(
            '.gmrc-guild-footer',
            $css
        );
    }
}
