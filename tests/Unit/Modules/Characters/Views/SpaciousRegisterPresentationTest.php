<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class SpaciousRegisterPresentationTest extends TestCase
{
    public function testSpaciousRegisterStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-spacious-register',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'spacious-register.css'
        );
    }

    public function testRegisterUsesAtMostTwoDesktopColumns(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'spacious-register.css'
        );

        self::assertIsString($styles);

        self::assertMatchesRegularExpression(
            '/repeat\(\s*2,\s*minmax\(0,\s*1fr\)\s*\)/s',
            $styles
        );

        self::assertStringNotContainsString(
            'auto-fit',
            $styles
        );
    }

    public function testRegisterFallsBackToOneColumn(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'spacious-register.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            '@media (max-width: 1100px)',
            $styles
        );

        self::assertMatchesRegularExpression(
            '/@media \(max-width: 1100px\).*?grid-template-columns:\s*minmax\(0,\s*1fr\)/s',
            $styles
        );
    }

    public function testCharacterNamesUseNaturalWrapping(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'spacious-register.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            'overflow-wrap: break-word',
            $styles
        );

        self::assertStringContainsString(
            'word-break: normal',
            $styles
        );
    }
}
