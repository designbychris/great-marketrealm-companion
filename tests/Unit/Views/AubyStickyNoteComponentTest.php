<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyStickyNoteComponentTest extends TestCase
{
    public function testStickyNoteComponentAssetsExist(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/app/Views/components/auby/'
            . 'sticky-note.php'
        );

        self::assertFileExists(
            $root
            . '/assets/css/components/auby/'
            . 'sticky-note.css'
        );

        self::assertFileExists(
            $root
            . '/assets/js/components/auby/'
            . 'sticky-note.js'
        );
    }

    public function testStickyNoteSupportsPurpleTapeAndReducedMotion(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/auby/'
            . 'sticky-note.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '#6d3d88',
            strtolower($css)
        );

        self::assertStringContainsString(
            'prefers-reduced-motion: reduce',
            $css
        );
    }
}
