<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class LivingGuildFoundationTest extends TestCase
{
    public function testLivingGuildManifestAndControllerExist(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/assets/data/guild-hall/'
            . 'living-guild.json'
        );

        self::assertFileExists(
            $root
            . '/assets/js/components/guild-hall/'
            . 'living-guild.js'
        );
    }

    public function testLivingGuildUsesOneStoryBeatAtATime(): void
    {
        $root = dirname(__DIR__, 3);

        $script = file_get_contents(
            $root
            . '/assets/js/components/guild-hall/'
            . 'living-guild.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'clearBeat(desk)',
            $script
        );

        self::assertStringContainsString(
            'activateBeat(desk, beat)',
            $script
        );

        self::assertStringContainsString(
            '7600',
            $script
        );
    }

    public function testCopperCoinIsAccessibleAndDoesNotAlterCurrency(): void
    {
        $root = dirname(__DIR__, 3);

        $view = file_get_contents(
            $root
            . '/app/Views/components/guild-hall/'
            . 'auby-desk.php'
        );

        $script = file_get_contents(
            $root
            . '/assets/js/components/guild-hall/'
            . 'living-guild.js'
        );

        self::assertIsString($view);
        self::assertIsString($script);

        self::assertStringContainsString(
            'data-living-guild-coin',
            $view
        );

        self::assertStringContainsString(
            'Pick up the copper coin',
            $view
        );

        self::assertStringContainsString(
            'sessionStorage',
            $script
        );

        self::assertStringNotContainsString(
            'character.gold',
            $script
        );
    }
}
