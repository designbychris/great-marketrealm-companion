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

    public function testGuildCertificationIsLockedUntilSealIsReady(): void
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
            'Guild Certification remains locked.',
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
            '$advancement[\'folios\']',
            $view
        );

        self::assertStringContainsString(
            'data-rising-folio=',
            $view
        );

        self::assertStringContainsString(
            '$folio[\'label\']',
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
            'admin-post.php',
            $view
        );

        self::assertStringContainsString(
            "\$choiceMode === 'single'",
            $view
        );

        self::assertStringContainsString(
            "? 'choice'",
            $view
        );

        self::assertStringContainsString(
            ": 'choice[]'",
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
            'pending advancement',
            $view
        );
    }

    public function testAdvancementLedgerShowsAdvancementSealReview(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-advancement-seal',
            $view
        );

        self::assertStringContainsString(
            'Registrar’s Final Review',
            $view
        );

        self::assertStringContainsString(
            'components.auby.seal-of-approval',
            $view
        );

        self::assertStringContainsString(
            "'context' => 'advancement'",
            $view
        );

        self::assertStringContainsString(
            'No Character changes have been applied.',
            $view
        );
    }
    public function testSealedAdvancementCanBeGuildCertified(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-guild-certification',
            $view
        );

        self::assertStringContainsString(
            '/progression/advance/certify',
            $view
        );

        self::assertStringContainsString(
            'Certify Advancement',
            $view
        );

        self::assertStringContainsString(
            'cannot be applied twice',
            $view
        );
    }

    public function testCallingFolioCanShowDelegatedSpecialistFolios(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "folio['delegated']",
            $view
        );

        self::assertStringContainsString(
            'Specialist folios identified',
            $view
        );

        self::assertStringContainsString(
            'Assigned to Phase',
            $view
        );
    }

    public function testSpellbookFoliosSupportChooseNCheckboxes(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/advancement.php'
        );
        self::assertIsString($view);
        self::assertStringContainsString("choice_mode", $view);
        self::assertStringContainsString("choice[]", $view);
        self::assertStringContainsString("selected_values", $view);
    }

}
