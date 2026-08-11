<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class PrivateStudioLayoutFinishTest extends TestCase
{
    public function testCreatorWorkbenchUsesTwoLineControlCards(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-dressing-table.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            'Phase III.7.3.2.1.1 — Creator Workbench layout finish',
            $styles
        );

        self::assertStringContainsString(
            '"label label label label"',
            $styles
        );

        self::assertStringContainsString(
            '"previous random next position"',
            $styles
        );
    }

    public function testLedgerPortraitAndUploadToolkitUseTwoColumns(): void
    {
        $root = dirname(__DIR__, 5);

        $styles = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'illuminators-private-studio.css'
        );

        self::assertIsString($styles);

        self::assertStringContainsString(
            'Phase III.7.3.2.1.1 — Finished Ledger portrait desk',
            $styles
        );

        self::assertStringContainsString(
            'minmax(15rem, 0.9fr)',
            $styles
        );

        self::assertStringContainsString(
            'minmax(17rem, 1.1fr)',
            $styles
        );

        self::assertStringContainsString(
            '@media (max-width: 920px)',
            $styles
        );
    }
}
