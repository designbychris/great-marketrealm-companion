<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GreatPortraitExpansionDressingTableTest extends TestCase
{
    public function testWorkbenchUsesBodyFormTerminology(): void
    {
        $root = dirname(__DIR__, 5);

        $controls = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'controls.js'
        );

        self::assertIsString($controls);

        self::assertStringContainsString(
            "['body', 'Body form']",
            $controls
        );

        self::assertStringNotContainsString(
            "['body', 'Heritage form']",
            $controls
        );
    }

    public function testDressingTableHasPhaseTwoRefinement(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-dressing-table.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            'Phase III.7.3.2 — Dressing Table refinement',
            $styles
        );

        self::assertStringContainsString(
            '.gmrc-portrait-controls__actions::before',
            $styles
        );
    }
}
