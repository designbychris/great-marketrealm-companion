<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class LivingPortraitAssetContractTest extends TestCase
{
    public function testLivingPortraitAssetsExist(): void
    {
        $root = dirname(__DIR__, 6);

        self::assertFileExists(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/living-portrait.js'
        );

        self::assertFileExists(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Collections/'
            . 'FructanGrocer/Assets/'
            . 'auby-illuminator-mark.svg'
        );
    }

    public function testMotionStylesRespectReducedMotion(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            'prefers-reduced-motion: reduce',
            $css
        );
        self::assertStringContainsString(
            'animation: none !important',
            $css
        );
    }
}
