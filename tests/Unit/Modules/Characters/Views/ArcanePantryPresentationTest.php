<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class ArcanePantryPresentationTest extends TestCase
{
    public function testOpenLedgerContainsArcanePantryTabAndPanel(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-ledger-tab="arcana"',
            $view
        );
        self::assertStringContainsString(
            'data-ledger-panel="arcana"',
            $view
        );
        self::assertStringContainsString(
            'Spells & Abilities',
            $view
        );
        self::assertStringContainsString(
            'The Arcane Pantry',
            $view
        );
    }

    public function testArcanePantryProvidesAccessibleIndexedShelves(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/arcane-pantry.js'
        );

        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString('data-arcane-index', $view);
        self::assertStringContainsString('role="tablist"', $view);
        self::assertStringContainsString('data-arcane-tab=', $view);
        self::assertStringContainsString('data-arcane-panel=', $view);
        self::assertStringContainsString('aria-selected=', $view);
        self::assertStringContainsString('aria-controls=', $view);
        self::assertStringContainsString('$arcana[\'shelves\']', $view);
        self::assertStringContainsString("event.key === 'ArrowRight'", $script);
        self::assertStringContainsString("event.key === 'ArrowLeft'", $script);
        self::assertStringContainsString("event.key === 'Home'", $script);
        self::assertStringContainsString("event.key === 'End'", $script);
        self::assertStringContainsString("candidate.setAttribute(", $script);
        self::assertStringContainsString("panel.hidden = !active;", $script);
    }

    public function testArcanePantryStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            'gmrc-arcane-pantry',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );
        self::assertFileExists(
            $root
            . '/assets/js/modules/characters/'
            . 'arcane-pantry.js'
        );
        self::assertStringContainsString(
            'gmrc-arcane-pantry',
            $provider
        );
        self::assertStringContainsString(
            "'arcane-pantry.js'",
            $provider
        );
    }

    public function testGuildDiceUnderstandsHealingFormulaRolls(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            "selection.kind === 'healing'",
            $script
        );
        self::assertStringContainsString(
            "'Healing Roll'",
            $script
        );
    }
}
