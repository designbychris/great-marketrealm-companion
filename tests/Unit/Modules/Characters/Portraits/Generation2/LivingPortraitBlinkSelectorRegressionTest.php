<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class LivingPortraitBlinkSelectorRegressionTest extends TestCase
{
    public function testBlinkSelectorsTargetReadyPortraitItself(): void
    {
        $root = dirname(__DIR__, 6);

        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);

        $readySelector =
            '[data-portrait-generation="2"]'
            . '[data-illumination-ready="true"]';

        self::assertStringContainsString(
            $readySelector,
            $css
        );

        self::assertStringContainsString(
            $readySelector
            . "\n"
            . '.gmrc-g2-eyes.is-blinking',
            $css
        );

        self::assertStringContainsString(
            $readySelector
            . "\n"
            . '.gmrc-g2-eyelids.is-blinking',
            $css
        );

        self::assertStringNotContainsString(
            '[data-portrait-generation="2"]'
            . "\n"
            . '[data-illumination-ready="true"]',
            $css
        );
    }

    public function testDoubleBlinkUsesSharedBlinkState(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/living-portrait.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'const setBlinkState',
            $script
        );

        self::assertStringContainsString(
            'setBlinkState(true)',
            $script
        );

        self::assertStringContainsString(
            'setBlinkState(false)',
            $script
        );

        self::assertStringNotContainsString(
            'eyes.classList.remove',
            $script
        );

        self::assertStringNotContainsString(
            'eyelids.classList.remove',
            $script
        );
    }
}
