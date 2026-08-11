<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskFourSceneRestorationTest extends TestCase
{
    private const RESTORED_SCENES = [
        'morning',
        'afternoon',
        'evening',
        'late-night',
    ];

    private const SCENE_ALIASES = [
        'dawn' => 'morning',
        'night' => 'late-night',
    ];

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
            self::RESTORED_SCENES,
            $restoration['scenes'] ?? null
        );

        self::assertSame(
            self::SCENE_ALIASES,
            $restoration['aliases'] ?? null
        );
    }

    public function testRestoredRuntimeScenesAreHighResolution(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (self::RESTORED_SCENES as $scene) {
            $path =
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'high-resolution/auby-desk-'
                . $scene
                . '-hires.webp';

            self::assertFileExists($path);

            $size = getimagesize($path);

            self::assertIsArray($size);

            self::assertGreaterThanOrEqual(
                1500,
                $size[0]
            );

            self::assertGreaterThanOrEqual(
                900,
                $size[1]
            );
        }
    }

    public function testAliasScenesResolveToRestoredPhysicalScenes(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (
            self::SCENE_ALIASES
            as $alias => $target
        ) {
            self::assertContains(
                $target,
                self::RESTORED_SCENES
            );

            $targetPath =
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'high-resolution/auby-desk-'
                . $target
                . '-hires.webp';

            self::assertFileExists(
                $targetPath,
                sprintf(
                    'Alias "%s" points to missing scene "%s".',
                    $alias,
                    $target
                )
            );
        }
    }
}
