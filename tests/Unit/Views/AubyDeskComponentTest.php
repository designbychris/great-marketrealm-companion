<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskComponentTest extends TestCase
{
    public function testDeskComponentAndDaypartArtworkExist(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/app/Views/components/guild-hall/'
            . 'auby-desk.php'
        );

        foreach (
            [
                'morning',
                'day',
                'evening',
                'night',
            ] as $daypart
        ) {
            self::assertFileExists(
                $root
                . '/assets/images/auby/desk/'
                . 'auby-desk-'
                . $daypart
                . '.svg'
            );
        }
    }

    public function testDeskScriptUsesVisitorsLocalHour(): void
    {
        $root = dirname(__DIR__, 3);

        $script = file_get_contents(
            $root
            . '/assets/js/components/guild-hall/'
            . 'auby-desk.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'new Date().getHours()',
            $script
        );

        self::assertStringContainsString(
            "return 'morning'",
            $script
        );

        self::assertStringContainsString(
            "return 'evening'",
            $script
        );

        self::assertStringContainsString(
            "return 'night'",
            $script
        );
    }
}
