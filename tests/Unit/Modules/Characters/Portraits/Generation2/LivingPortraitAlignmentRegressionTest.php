<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class LivingPortraitAlignmentRegressionTest extends TestCase
{
    public function testEyesAndEyelidsShareTheCommonBreathingGroup(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'generation2.js'
        );

        self::assertIsString($css);
        self::assertIsString($script);

        self::assertStringContainsString(
            '.gmrc-g2-breathing-group',
            $css
        );

        self::assertStringContainsString(
            'gmrc-g2-breathing-group',
            $script
        );

        self::assertStringContainsString(
            "'gmrc-g2-face-overlay'",
            $script
        );

        self::assertStringContainsString(
            '.gmrc-g2-eyes.is-blinking',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-g2-eyelids.is-blinking',
            $css
        );

        self::assertStringNotContainsString(
            'scaleY(0.075)',
            $css
        );
    }
}
