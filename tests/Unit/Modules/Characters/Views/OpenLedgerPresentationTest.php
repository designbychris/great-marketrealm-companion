<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class OpenLedgerPresentationTest extends TestCase
{
    public function testOpenLedgerUsesTwoBookSpreads(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'gmrc-open-ledger',
            $view
        );

        self::assertGreaterThanOrEqual(
            2,
            substr_count(
                $view,
                'gmrc-ledger-book'
            )
        );

        self::assertStringContainsString(
            'gmrc-ledger-page--identity',
            $view
        );

        self::assertStringContainsString(
            'gmrc-ledger-page--measures',
            $view
        );

        self::assertStringContainsString(
            'gmrc-ledger-page--skills',
            $view
        );

        self::assertStringContainsString(
            'gmrc-ledger-page--archive',
            $view
        );
    }

    public function testOpenLedgerPreservesCharacterLifecycleActions(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'Return to Register',
            $view
        );

        self::assertStringContainsString(
            'Edit Adventurer',
            $view
        );

        self::assertStringContainsString(
            'Delete Adventurer',
            $view
        );

        self::assertStringContainsString(
            '/delete',
            $view
        );
    }

    public function testOpenLedgerKeepsCurrentCharacterRecordSections(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        foreach (
            [
                'Ability Scores',
                'Hit Points',
                'Saving Throws',
                'Skills',
                'Background',
                'Languages',
                'Tool Proficiencies',
                'Conditions',
                'Leather Satchel',
                'Honours',
            ] as $section
        ) {
            self::assertStringContainsString(
                $section,
                $view
            );
        }
    }
}
