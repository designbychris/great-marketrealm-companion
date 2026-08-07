<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class AubySealAnimationContractTest extends TestCase
{
    public function testSealWaitsForIlluminationCompletion(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/auby/'
            . 'seal-of-approval.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'gmrc:portrait:illumination-complete',
            $script
        );

        self::assertStringContainsString(
            'data-auby-seal-trigger="manual"',
            $script
        );

        self::assertStringContainsString(
            'gmrc-auby-seal-surface--impact',
            $script
        );
    }

    public function testSealRespectsReducedMotion(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/auby/'
            . 'seal-of-approval.css'
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
