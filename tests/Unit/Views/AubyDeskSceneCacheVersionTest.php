<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskSceneCacheVersionTest extends TestCase
{
    public function testDeskPublishesSceneVersionToRuntime(): void
    {
        $root = dirname(__DIR__, 3);

        $view = file_get_contents(
            $root
            . '/app/Views/components/guild-hall/'
            . 'auby-desk.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-auby-scene-version',
            $view
        );

        self::assertStringContainsString(
            'filemtime',
            $view
        );

        self::assertStringContainsString(
            '?ver=',
            $view
        );
    }

    public function testRuntimeVersionsManifestAndSceneUrls(): void
    {
        $root = dirname(__DIR__, 3);

        $script = file_get_contents(
            $root
            . '/assets/js/components/guild-hall/'
            . 'auby-desk.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'versionedUrl',
            $script
        );

        self::assertStringContainsString(
            'aubySceneVersion',
            $script
        );

        self::assertStringContainsString(
            "base + 'manifest.json'",
            $script
        );

        self::assertStringContainsString(
            'base + scene.image',
            $script
        );
    }
}
