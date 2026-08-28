<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Tokens;

use PHPUnit\Framework\TestCase;

final class TokenForgeFolioRegressionTest extends TestCase
{
    public function testCharacterLedgerExposesDedicatedTabletopTokenTab(): void
    {
        $view = file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-ledger-tab="tabletop-token"', $view);
        self::assertStringContainsString('aria-controls="gmrc-ledger-panel-tabletop-token"', $view);
        self::assertStringContainsString('Tabletop Token', $view);
    }

    public function testTokenForgeLivesInsideItsOwnLedgerPanel(): void
    {
        $view = file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-ledger-panel="tabletop-token"', $view);
        self::assertStringContainsString('gmrc-ledger-book--tabletop-token', $view);
        self::assertStringContainsString("'components.media.tabletop-token-forge'", $view);
    }
}
