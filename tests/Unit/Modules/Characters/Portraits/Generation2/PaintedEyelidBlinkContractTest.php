<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class PaintedEyelidBlinkContractTest extends TestCase
{
    public function testDedicatedEyelidAssetExists(): void
    {
        $root = dirname(__DIR__, 6);

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Shared/Faces/'
            . 'Assets/eyelids/apple-closed-01.svg'
        );
    }

    public function testBlinkScriptTargetsOpenEyesAndPaintedEyelids(): void
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
            'eyes.classList.toggle',
            $script
        );

        self::assertStringContainsString(
            'eyelids.classList.toggle',
            $script
        );
    }

    public function testAubyFinishingTouchAssetExists(): void
    {
        $root = dirname(__DIR__, 6);

        self::assertFileExists(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Collections/'
            . 'FructanGrocer/Assets/'
            . 'auby-finishing-touch.svg'
        );
    }
}
