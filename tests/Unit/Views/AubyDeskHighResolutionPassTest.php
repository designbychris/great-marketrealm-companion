<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskHighResolutionPassTest extends TestCase
{
    public function testEveryDaypartHasHighResolutionArtwork(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (
            [
                'dawn',
                'morning',
                'afternoon',
                'evening',
                'night',
                'late-night',
            ] as $scene
        ) {
            self::assertFileExists(
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'high-resolution/auby-desk-'
                . $scene
                . '-hires.webp'
            );
        }
    }

    public function testManifestUsesHighResolutionPass(): void
    {
        $root = dirname(__DIR__, 3);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        self::assertSame(
            '3.1',
            $manifest['high_resolution_pass'] ?? null
        );

        foreach ($manifest['scenes'] ?? [] as $scene) {
            self::assertStringContainsString(
                'high-resolution/',
                (string) ($scene['image'] ?? '')
            );

            self::assertSame(
                'high-resolution-wide',
                $scene['composition'] ?? null
            );
        }
    }

    public function testDesktopPanelUsesFortyTwoPercentWidth(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
                . '/assets/css/components/guild-hall/'
                . 'auby-desk.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '42%',
            $css
        );

        self::assertStringContainsString(
            '#402617 80%',
            $css
        );
    }
}
