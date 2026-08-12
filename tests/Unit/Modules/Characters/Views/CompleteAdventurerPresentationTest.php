<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class CompleteAdventurerPresentationTest extends TestCase
{
    public function testLedgerProvidesRegistrarsFinalAudit(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-complete-adventurer', $view);
        self::assertStringContainsString('Registrar’s Final Audit', $view);
        self::assertStringContainsString('data-ledger-jump=', $view);
    }

    public function testLivingLedgerSupportsFolioJumpButtons(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString('[data-ledger-jump]', $script);
        self::assertStringContainsString('prefers-reduced-motion: reduce', $script);
    }
}
