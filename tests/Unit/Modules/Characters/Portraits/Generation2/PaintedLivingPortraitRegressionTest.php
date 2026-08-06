<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class PaintedLivingPortraitRegressionTest extends TestCase
{
    public function testRevealReleasesActiveAnimationClass(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/furniture/'
            . 'registrars-desk.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            "'gmrc-g2-illumination-active'",
            $script
        );
        self::assertStringContainsString(
            '2860',
            $script
        );
    }

    public function testBlinkHasReadinessFallback(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/living-portrait.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'MutationObserver',
            $script
        );
        self::assertStringContainsString(
            'data-illumination-ready',
            $script
        );
    }

    public function testPainterlyAssetsUseSafeEdgeFilter(): void
    {
        $root = dirname(__DIR__, 6);

        $asset = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Races/Fructan/'
            . 'Assets/apple/body-base.svg'
        );

        self::assertIsString($asset);
        self::assertStringContainsString(
            'id="painted-edge"',
            $asset
        );
        self::assertStringContainsString(
            '<feDisplacementMap',
            $asset
        );
        self::assertStringNotContainsString(
            '<script',
            strtolower($asset)
        );
    }
}
