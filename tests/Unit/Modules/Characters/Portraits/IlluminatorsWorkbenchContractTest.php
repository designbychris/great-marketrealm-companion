<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class IlluminatorsWorkbenchContractTest extends TestCase
{
    public function testLayerUpdaterUsesRenderedFaceSelectors(): void
    {
        $root = dirname(__DIR__, 5);

        $source = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'layer-updater.js'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            "eyes: '.gmrc-portrait-layer--eyes'",
            $source
        );

        self::assertStringContainsString(
            "mouth: '.gmrc-portrait-layer--mouth'",
            $source
        );

        self::assertStringNotContainsString(
            "eyes: '.gmrc-portrait-layer--face'",
            $source
        );
    }

    public function testClassLayerUsesAssetSlotContract(): void
    {
        $root = dirname(__DIR__, 5);

        $source = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'layer-updater.js'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'portraitAssetSlot',
            $source
        );

        self::assertStringContainsString(
            'replaceClassPart',
            $source
        );
    }

    public function testControlsOnlyExposeAdjustableLayers(): void
    {
        $root = dirname(__DIR__, 5);

        $source = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'controls.js'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            '.isAdjustable(slot)',
            $source
        );

        self::assertStringContainsString(
            'data-portrait-control-position',
            str_replace(
                'dataset.portraitControlPosition',
                'data-portrait-control-position',
                $source
            )
        );
    }

    public function testRecipeAppliedEventRefreshesResetBaseline(): void
    {
        $root = dirname(__DIR__, 5);

        $legacy = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio.js'
        );

        $app = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/app.js'
        );

        self::assertIsString($legacy);
        self::assertIsString($app);

        self::assertStringContainsString(
            'gmrc:portrait:recipe-applied',
            $legacy
        );

        self::assertStringContainsString(
            'refreshInitial()',
            $app
        );
    }
}
