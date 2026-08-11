<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class HeritagePortraitContractTest extends TestCase
{
    public function testCreatorResolvesCatalogueHeritageToConcreteAsset(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'select[name="heritage"]',
            $script
        );

        self::assertStringContainsString(
            "+ '-heritage-'",
            $script
        );

        self::assertStringContainsString(
            'portraitHeritageKey',
            $script
        );
    }

    public function testPersistedHeritageHasDedicatedRenderer(): void
    {
        $root = dirname(__DIR__, 5);

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/Rendering/Layers/'
            . 'HeritageLayerRenderer.php'
        );

        $provider = file_get_contents(
            $root
            . '/app/Modules/Characters/'
            . 'CharactersServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'HeritageLayerRenderer',
            $provider
        );
    }

    public function testGrandCatalogueDoesNotWriteRawKeyIntoPortraitAssetField(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'grand-catalogue.js'
        );

        self::assertIsString($script);

        self::assertStringNotContainsString(
            'querySelector(\'[name="portrait_heritage"]\')',
            $script
        );

        self::assertStringContainsString(
            'gmrc:catalogue:heritage-changed',
            $script
        );
    }
}
