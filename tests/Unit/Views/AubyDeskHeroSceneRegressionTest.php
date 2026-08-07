<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskHeroSceneRegressionTest extends TestCase
{
    public function testAfternoonUsesWideHeroArtwork(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/assets/images/auby/desk/scenes/'
            . 'auby-desk-afternoon-wide.webp'
        );

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        $afternoon = array_values(
            array_filter(
                $manifest['scenes'] ?? [],
                static fn (array $scene): bool =>
                    ($scene['id'] ?? null)
                    === 'afternoon'
            )
        )[0] ?? [];

        self::assertSame(
            'auby-desk-afternoon-wide.webp',
            $afternoon['image'] ?? null
        );

        self::assertSame(
            'hero-wide',
            $afternoon['composition'] ?? null
        );
    }

    public function testSleepEffectIsRestrictedToLateNight(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/guild-hall/'
            . 'auby-desk.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '[data-guild-hall-daypart="late-night"]',
            $css
        );

        self::assertStringContainsString(
            '[data-ambient~="sleep"]',
            $css
        );

        self::assertStringContainsString(
            'display: none',
            $css
        );
    }
}
