<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class LivingPortraitBlinkSelectorRegressionTest extends TestCase
{
    public function testBlinkSelectorTargetsReadyPortraitItself(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '[data-portrait-generation="2"]'
            . '[data-illumination-ready="true"]',
            $css
        );

        self::assertStringNotContainsString(
            '[data-portrait-generation="2"]'
            . "\n"
            . '[data-illumination-ready="true"]'
            . "\n"
            . '.gmrc-g2-eyelids.is-blinking',
            $css
        );
    }

    public function testDoubleBlinkUsesEyelidLayer(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/living-portrait.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'eyelids.classList.remove',
            $script
        );

        self::assertStringNotContainsString(
            'eyes.classList.remove',
            $script
        );
    }
}
