<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class DiceOfDestinyLayoutRegressionTest extends TestCase
{
    public function testDiceTableUsesThreeDesktopColumns(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'dice-of-destiny.css'
        );

        self::assertIsString($styles);

        self::assertMatchesRegularExpression(
            '/repeat\(\s*3,\s*minmax\(0,\s*1fr\)\s*\)/s',
            $styles
        );
    }

    public function testDiceTableFallsBackToTwoThenOneColumn(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'dice-of-destiny.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            '@media (max-width: 1050px)',
            $styles
        );

        self::assertMatchesRegularExpression(
            '/repeat\(\s*2,\s*minmax\(0,\s*1fr\)\s*\)/s',
            $styles
        );

        self::assertStringContainsString(
            '@media (max-width: 680px)',
            $styles
        );
    }
}
