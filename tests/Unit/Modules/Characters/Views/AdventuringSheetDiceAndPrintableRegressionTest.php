<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdventuringSheetDiceAndPrintableRegressionTest extends TestCase
{
    public function test_live_sheet_keeps_guild_dice_off_by_default_and_remembers_the_local_preference(): void
    {
        $root = dirname(__DIR__, 5);
        $view = (string) file_get_contents($root . '/app/Modules/Characters/Views/adventuring-sheet.php');
        $script = (string) file_get_contents($root . '/assets/js/modules/characters/adventuring-sheet.js');
        $dice = (string) file_get_contents($root . '/assets/js/modules/characters/guild-dice.js');

        self::assertStringContainsString('data-guild-dice-enabled="false"', $view);
        self::assertStringContainsString('data-adventuring-dice-toggle', $view);
        self::assertStringContainsString('aria-checked="false"', $view);
        self::assertStringContainsString('gmrc.adventuringSheet.guildDiceEnabled', $script);
        self::assertStringContainsString("getItem(STORAGE_KEY) === 'true'", $script);
        self::assertStringContainsString("[data-living-ledger], [data-guild-dice-surface]", $dice);
    }

    public function test_live_sheet_exposes_core_rolls_through_the_existing_guild_dice_contract(): void
    {
        $root = dirname(__DIR__, 5);
        $view = (string) file_get_contents($root . '/app/Modules/Characters/Views/adventuring-sheet.php');

        self::assertStringContainsString('data-roll-kind="initiative"', $view);
        self::assertStringContainsString('data-roll-kind="ability"', $view);
        self::assertStringContainsString('data-roll-kind="saving-throw"', $view);
        self::assertStringContainsString('data-roll-kind="skill"', $view);
        self::assertStringContainsString('data-roll-kind="attack"', $view);
        self::assertStringContainsString('data-roll-kind="damage"', $view);
        self::assertStringContainsString('data-roll-kind="spell-attack"', $view);
        self::assertStringContainsString("require __DIR__ . '/partials/guild-dice-tray.php'", $view);
    }

    public function test_offline_sheet_has_a_dedicated_print_route_and_a4_print_contract_without_dice_controls(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = (string) file_get_contents($root . '/app/Modules/Characters/Routes.php');
        $controller = (string) file_get_contents($root . '/app/Modules/Characters/Controllers/CharacterController.php');
        $view = (string) file_get_contents($root . '/app/Modules/Characters/Views/printable-sheet.php');
        $css = (string) file_get_contents($root . '/assets/css/modules/characters/printable-sheet.css');
        $script = (string) file_get_contents($root . '/assets/js/modules/characters/printable-sheet.js');

        self::assertStringContainsString("'/characters/{id}/printable-sheet'", $routes);
        self::assertStringContainsString("[CharacterController::class, 'printableSheet']", $routes);
        self::assertStringContainsString('public function printableSheet(', $controller);
        self::assertStringContainsString("'characters.printable-sheet'", $controller);
        self::assertStringContainsString('data-printable-sheet', $view);
        self::assertStringContainsString('Print / Save PDF', $view);
        self::assertStringContainsString('Current HP', $view);
        self::assertStringContainsString('Temporary HP', $view);
        self::assertStringNotContainsString('data-guild-roll=', $view);
        self::assertStringNotContainsString('data-adventuring-dice-toggle', $view);
        self::assertStringContainsString('@page{size:A4 portrait', $css);
        self::assertStringContainsString('@media print', $css);
        self::assertStringContainsString('window.print()', $script);
        self::assertStringContainsString('data-print-character-name', $view);
        self::assertStringContainsString('gmrc-printable-sheet__print-brand', $view);
        self::assertStringContainsString('gmrc-printable-sheet__page-footer', $view);
        self::assertStringContainsString('document.title = suggestedTitle()', $script);
        self::assertStringContainsString('gmrc-print-excluded', $script);
        self::assertStringContainsString('.gmrc-print-mode .gmrc-print-excluded', $css);
        self::assertStringContainsString('.gmrc-print-block--skills', $css);
        self::assertStringContainsString('grid-row: auto !important', $css);
    }
}
