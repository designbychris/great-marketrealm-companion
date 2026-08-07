<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class TruePaintedBlinkRegressionTest extends TestCase
{
    public function testBlinkScriptTogglesEyesAndEyelidsTogether(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/living-portrait.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            "'.gmrc-g2-eyes'",
            $script
        );

        self::assertStringContainsString(
            "'.gmrc-g2-eyelids'",
            $script
        );

        self::assertStringContainsString(
            'const setBlinkState',
            $script
        );

        self::assertStringContainsString(
            "eyes.classList.toggle",
            $script
        );

        self::assertStringContainsString(
            "eyelids.classList.toggle",
            $script
        );
    }

    public function testOpenEyesHideDuringPaintedBlink(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '.gmrc-g2-eyes.is-blinking',
            $css
        );

        self::assertStringContainsString(
            'opacity: 0 !important',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-g2-eyelids.is-blinking',
            $css
        );

        self::assertStringContainsString(
            'opacity: 1 !important',
            $css
        );
    }
}
