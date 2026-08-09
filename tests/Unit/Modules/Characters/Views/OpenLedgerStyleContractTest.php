<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class OpenLedgerStyleContractTest extends TestCase
{
    public function testOpenLedgerStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-open-ledger',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'open-ledger.css'
        );
    }

    public function testLedgerUsesBookBindingAndHandwrittenTypography(): void
    {
        $root = dirname(__DIR__, 5);

        $css = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'open-ledger.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            'gmrc-ledger-book__binding',
            $css
        );

        self::assertStringContainsString(
            '--gmrc-font-handwritten',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 820px)',
            $css
        );
    }
}
