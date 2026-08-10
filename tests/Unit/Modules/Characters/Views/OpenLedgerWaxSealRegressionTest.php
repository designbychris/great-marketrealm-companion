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

        $end = strpos(
            $css,
            '}',
            $start
        );

        self::assertNotFalse($end);

        $block = substr(
            $css,
            $start,
            ($end - $start) + 1
        );

        self::assertStringContainsString(
            'position: absolute;',
            $block
        );

        self::assertStringContainsString(
            'right:',
            $block
        );

        self::assertStringContainsString(
            'bottom:',
            $block
        );

        self::assertStringNotContainsString(
            "\n    top:",
            $block
        );
    }
}
