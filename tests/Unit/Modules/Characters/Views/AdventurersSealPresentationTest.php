<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdventurersSealPresentationTest extends TestCase
{
    public function testSealIsDerivedFromCompleteAuditState(): void
    {
        $root = dirname(__DIR__, 5);

        $presenter = file_get_contents(
            $root
            . '/app/Modules/Characters/Services/'
            . 'CompleteAdventurerPresenter.php'
        );

        self::assertIsString($presenter);

        self::assertStringContainsString(
            "'certified' => \$complete",
            $presenter
        );

        self::assertStringContainsString(
            "'seal_title'",
            $presenter
        );

        self::assertStringContainsString(
            "'seal_status'",
            $presenter
        );
    }

    public function testLedgerRendersGoldAubySealOnlyWhenCertified(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "completeAdventurer['certified']",
            $view
        );

        self::assertStringContainsString(
            'data-adventurers-seal',
            $view
        );

        self::assertStringContainsString(
            "'variant' => 'gold'",
            $view
        );

        self::assertStringContainsString(
            'Certified by the Guild Registrar',
            $view
        );
    }
}
