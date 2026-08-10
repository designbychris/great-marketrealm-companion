<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskLedgerRestorationPassTest extends TestCase
{
    public function testManifestDeclaresLedgerRestorationPass(): void
    {
        $root = dirname(__DIR__, 3);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        self::assertSame(
            '3.1.2',
            $manifest['ledger_restoration_pass']['version']
                ?? null
        );

        self::assertSame(
            '3200x1980',
            $manifest['ledger_restoration_pass']['runtime_resolution']
                ?? null
        );
    }
}
