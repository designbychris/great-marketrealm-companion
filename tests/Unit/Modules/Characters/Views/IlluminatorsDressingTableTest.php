<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class IlluminatorsDressingTableTest extends TestCase
{
    public function testDressingTableStylesAreRegisteredAfterWorkbench(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        $workbench = strpos(
            $provider,
            "'gmrc-illuminators-workbench-polish'"
        );

        $dressingTable = strpos(
            $provider,
            "'gmrc-illuminators-dressing-table'"
        );

        self::assertNotFalse($workbench);
        self::assertNotFalse($dressingTable);
        self::assertGreaterThan(
            $workbench,
            $dressingTable
        );
    }

    public function testDressingTableIsScopedToCharacterCreation(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-dressing-table.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            '.gmrc-character-creator',
            $styles
        );

        self::assertStringContainsString(
            '.gmrc-portrait-controls__actions',
            $styles
        );

        self::assertStringContainsString(
            '.gmrc-portrait-controls__position',
            $styles
        );
    }

    public function testDressingTableKeepsAccessibleFocusAndReducedMotion(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-dressing-table.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            ':focus-visible',
            $styles
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $styles
        );
    }
}
