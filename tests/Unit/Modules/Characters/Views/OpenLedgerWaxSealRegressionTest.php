<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class OpenLedgerWaxSealRegressionTest extends TestCase
{
    public function testWaxSealIsAnchoredToLowerRightOfPage(): void
    {
        $root = dirname(__DIR__, 5);

        $css = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'open-ledger.css'
        );

        self::assertIsString($css);

        $start = strpos(
            $css,
            '.gmrc-ledger-page__seal {'
        );

        self::assertNotFalse($start);

        $block = substr(
            $css,
            $start,
            420
        );

        self::assertStringContainsString(
            'bottom:',
            $block
        );

        self::assertStringContainsString(
            'right:',
            $block
        );

        self::assertStringNotContainsString(
            'top:',
            $block
        );
    }
}
