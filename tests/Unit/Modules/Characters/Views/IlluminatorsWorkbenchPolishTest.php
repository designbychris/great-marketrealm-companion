<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class IlluminatorsWorkbenchPolishTest extends TestCase
{
    public function testWorkbenchPolishStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-illuminators-workbench-polish',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-workbench-polish.css'
        );
    }

    public function testWorkbenchProvidesVariantPositionStyling(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-workbench-polish.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            '.gmrc-portrait-controls__position',
            $styles
        );

        self::assertStringContainsString(
            '.gmrc-portrait-controls__locked',
            $styles
        );
    }
}
