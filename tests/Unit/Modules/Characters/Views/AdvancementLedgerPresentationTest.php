<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdvancementLedgerPresentationTest extends TestCase
{
    public function testRisingRegisterProvidesBeginAdvancementAction(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'Begin Advancement',
            $view
        );
        self::assertStringContainsString(
            'progression/advance',
            $view
        );
        self::assertStringContainsString(
            'does not change the character automatically',
            $view
        );
    }

    public function testAdvancementLedgerIsReadOnlyInFoundationPass(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-advancement-ledger',
            $view
        );
        self::assertStringContainsString(
            'Advancement commit is intentionally locked.',
            $view
        );
        self::assertStringContainsString(
            'one at a time',
            $view
        );
    }
}
