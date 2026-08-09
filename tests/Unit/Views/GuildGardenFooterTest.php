<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class GuildGardenFooterTest extends TestCase
{
    public function testAppLayoutContainsGuildGardenFooter(): void
    {
        $root = dirname(__DIR__, 3);

        $layout = file_get_contents(
            $root
            . '/app/Core/View/Templates/layouts/'
            . 'app.php'
        );

        self::assertIsString($layout);

        self::assertStringContainsString(
            'gmrc-guild-footer',
            $layout
        );

        self::assertStringContainsString(
            'Where adventure meets ingredients.',
            $layout
        );
    }

    public function testFooterArtworkExistsAndIsReferenced(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/assets/images/guild-hall/footer/'
            . 'guild-garden-fence.webp'
        );

        self::assertFileExists(
            $root
            . '/art/IllustrationKit/GuildHall/Footer/'
            . 'guild-garden-fence-master.png'
        );

        $css = file_get_contents(
            $root
            . '/assets/css/components/navigation/'
            . 'guild-navigation.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            'guild-garden-fence.webp',
            $css
        );
    }
}
