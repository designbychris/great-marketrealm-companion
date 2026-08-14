<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class PathGiftPresentationTest extends TestCase
{
    public function testAdvancementLedgerRendersAutomaticPathGifts(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "=== 'path-gifts'",
            $view
        );

        self::assertStringContainsString(
            'automatic Path gifts',
            $view
        );

        self::assertStringContainsString(
            'Registrar’s catch-up',
            $view
        );
    }

    public function testOpenLedgerCanShowCertifiedPathGifts(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'gmrc-path-gifts-ledger',
            $view
        );

        self::assertStringContainsString(
            'Gifts of the Path',
            $view
        );
    }
}
