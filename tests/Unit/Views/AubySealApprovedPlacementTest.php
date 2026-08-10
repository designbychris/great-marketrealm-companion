<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubySealApprovedPlacementTest extends TestCase
{
    public function testPrimarySealUsesCurvedTextPaths(): void
    {
        $root = dirname(__DIR__, 3);

        $seal = file_get_contents(
            $root
            . '/assets/images/auby/seals/'
            . 'seal-of-approval.svg'
        );

        self::assertIsString($seal);

        self::assertStringContainsString(
            '<textPath',
            $seal
        );

        self::assertStringContainsString(
            '>SEAL OF</textPath>',
            $seal
        );

        self::assertStringContainsString(
            '>APPROVAL</textPath>',
            $seal
        );
    }

    public function testAubyNoteUsesSealInsteadOfOldPortraitMedallion(): void
    {
        $root = dirname(__DIR__, 3);

        $view = file_get_contents(
            $root
            . '/app/Views/components/furniture/'
            . 'auby-note.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'auby-note__seal',
            $view
        );

        self::assertStringNotContainsString(
            'auby-note__portrait-image',
            $view
        );
    }

    public function testIlluminatorOwnsSealInsideCanvas(): void
    {
        $root = dirname(__DIR__, 3);

        $view = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'illuminated-portrait.php'
        );

        self::assertIsString($view);

        $canvas = strpos(
            $view,
            'gmrc-illuminated-portrait__canvas'
        );

        $seal = strpos(
            $view,
            "'context' => 'portrait'"
        );

        $caption = strpos(
            $view,
            'gmrc-illuminated-portrait__caption'
        );

        self::assertIsInt($canvas);
        self::assertIsInt($seal);
        self::assertIsInt($caption);

        self::assertLessThan($seal, $canvas);
        self::assertLessThan($caption, $seal);
    }

    public function testOldCircularGuildMarkIsNoLongerInBenchmarkManifest(): void
    {
        $root = dirname(__DIR__, 3);

        $manifest = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Collections/'
            . 'FructanGrocer/manifest.json'
        );

        self::assertIsString($manifest);

        self::assertStringNotContainsString(
            'g2-auby-illuminator-mark-01',
            $manifest
        );

        self::assertStringNotContainsString(
            '"id": "g2-auby-finishing-touch-01"',
            $manifest
        );
    }
}
