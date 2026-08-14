<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class PathOfCallingPresentationTest extends TestCase
{
    public function testAdvancementLedgerExplainsPermanentPathChoice(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "\$folio['key'] === 'path'",
            $view
        );

        self::assertStringContainsString(
            'permanent Guild Record',
            $view
        );

        self::assertStringContainsString(
            "['path_label']",
            $view
        );
    }

    public function testOpenLedgerCanDisplayCertifiedCallingPath(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'callingPath()',
            $view
        );

        self::assertStringContainsString(
            '$callingPathLabel',
            $view
        );
    }
}
