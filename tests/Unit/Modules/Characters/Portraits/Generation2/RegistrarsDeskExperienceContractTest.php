<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class RegistrarsDeskExperienceContractTest extends TestCase
{
    public function testRegistrarDeskExperienceAssetsExist(): void
    {
        $root = dirname(__DIR__, 6);

        self::assertFileExists(
            $root
            . '/assets/js/components/furniture/'
            . 'registrars-desk.js'
        );

        self::assertFileExists(
            $root
            . '/assets/css/components/furniture/'
            . 'registrars-desk-reveal.css'
        );

        self::assertFileExists(
            $root
            . '/assets/images/auby/'
            . 'auby-note-face.svg'
        );
    }

    public function testRevealHonoursReducedMotion(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/furniture/'
            . 'registrars-desk-reveal.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            'prefers-reduced-motion: reduce',
            $css
        );
        self::assertStringContainsString(
            'opacity: 1 !important',
            $css
        );
    }

    public function testPollenUsesIndependentDepthLayers(): void
    {
        $root = dirname(__DIR__, 6);

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Shared/Effects/'
            . 'Assets/golden-pollen-far-01.svg'
        );

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Shared/Effects/'
            . 'Assets/golden-pollen-near-01.svg'
        );
    }
}
