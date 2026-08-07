<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskTypographyContractTest extends TestCase
{
    public function testApprovedDeskTypographyIsPresent(): void
    {
        $root = dirname(__DIR__, 3);

        $css = file_get_contents(
            $root
            . '/assets/css/components/guild-hall/'
            . 'auby-desk.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            'text-align: center',
            $css
        );

        self::assertStringContainsString(
            'letter-spacing: 0.12em',
            $css
        );

        self::assertStringContainsString(
            'max-width: 12ch',
            $css
        );
    }
}
