<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class LivingPortraitAlignmentRegressionTest extends TestCase
{
    public function testEyesAndEyelidsShareFaceBreathingMotion(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '.gmrc-g2-eyes,',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-g2-eyelids,',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-g2-eyes.is-blinking',
            $css
        );

        self::assertStringNotContainsString(
            'scaleY(0.075)',
            $css
        );
    }
}
