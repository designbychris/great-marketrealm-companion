<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskFourSceneRestorationTest extends TestCase
{
    public function testManifestUsesFourRestoredVisualScenes(): void
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

        $restoration =
            $manifest['four_scene_restoration']
            ?? [];

        self::assertSame(
            [
                'morning',
                'afternoon',
                'evening',
                'late-night',
            ],
            $restoration['scenes'] ?? null
        );

        self::assertSame(
            'morning',
            $restoration['aliases']['dawn'] ?? null
        );

        self::assertSame(
            'late-night',
            $restoration['aliases']['night'] ?? null
        );
    }

    public function testRestoredRuntimeScenesAreHighResolution(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (
            [
                'morning',
                'afternoon',
                'evening',
                'late-night',
            ]
            as $scene
        ) {
            $path =
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'high-resolution/auby-desk-'
                . $scene
                . '-hires.webp';

            self::assertFileExists($path);

            $size = getimagesize($path);

            self::assertIsArray($size);
            self::assertSame(2560, $size[0]);
            self::assertSame(1584, $size[1]);
        }
    }
}
