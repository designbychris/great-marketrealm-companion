<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskHeroLibraryTest extends TestCase
{
    public function testAllSixHeroScenesExist(): void
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
                . '/assets/images/auby/desk/scenes/high-resolution/'
                . 'auby-desk-'
                . $scene
                . '-hires.webp'
            );
        }
    }

    public function testManifestUsesHeroSceneLibrary(): void
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
            '2.2',
            $manifest['hero_library_version'] ?? null
        );

        foreach (
            $manifest['scenes'] ?? []
            as $scene
        ) {
            self::assertStringContainsString(
                'high-resolution/',
                (string) (
                    $scene['image'] ?? ''
                )
            );

            self::assertStringEndsWith(
                '-hires.webp',
                (string) (
                    $scene['image'] ?? ''
                )
            );
        }
    }

    public function testOnlyLateNightSceneEnablesSleep(): void
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

        foreach (
            $manifest['scenes'] ?? []
            as $scene
        ) {
            $effects =
                $scene['ambient_effects']
                ?? [];

            if (
                ($scene['id'] ?? '')
                === 'late-night'
            ) {
                self::assertContains(
                    'sleep',
                    $effects
                );

                continue;
            }

            self::assertNotContains(
                'sleep',
                $effects
            );
        }
    }
}
