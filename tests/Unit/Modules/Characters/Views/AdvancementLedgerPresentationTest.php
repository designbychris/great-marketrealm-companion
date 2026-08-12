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

    public function testAdvancementLedgerShowsRisingFolios(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-rising-folios',
            $view
        );

        self::assertStringContainsString(
            'Vitality',
            $view
        );

        self::assertStringContainsString(
            'Proficiency',
            $view
        );

        self::assertStringContainsString(
            'folios ready',
            $view
        );
    }
    public function testVitalityChoiceFolioIsInteractive(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            '/progression/advance/choice',
            $view
        );

        self::assertStringContainsString(
            'name="choice"',
            $view
        );

        self::assertStringContainsString(
            'Record Choice',
            $view
        );

        self::assertStringContainsString(
            'Update Choice',
            $view
        );

        self::assertStringContainsString(
            'temporary advancement',
            $view
        );
    }

}
