<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdventuringSheetRegressionTest extends TestCase
{
    public function test_adventuring_sheet_has_a_dedicated_owned_character_route(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = (string) file_get_contents($root . '/app/Modules/Characters/Routes.php');
        $controller = (string) file_get_contents($root . '/app/Modules/Characters/Controllers/CharacterController.php');

        self::assertStringContainsString("'/characters/{id}/adventuring-sheet'", $routes);
        self::assertStringContainsString("[CharacterController::class, 'adventuringSheet']", $routes);
        self::assertStringContainsString('public function adventuringSheet(', $controller);
        self::assertStringContainsString("'characters.adventuring-sheet'", $controller);
    }

    public function test_full_ledger_links_to_the_focused_live_play_sheet(): void
    {
        $root = dirname(__DIR__, 5);
        $ledger = (string) file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertStringContainsString(". '/adventuring-sheet'", $ledger);
        self::assertStringContainsString("'label' => 'Adventuring Sheet'", $ledger);
    }

    public function test_sheet_keeps_live_play_information_on_one_responsive_surface(): void
    {
        $root = dirname(__DIR__, 5);
        $view = (string) file_get_contents($root . '/app/Modules/Characters/Views/adventuring-sheet.php');
        $css = (string) file_get_contents($root . '/assets/css/modules/characters/adventuring-sheet.css');
        $frontend = (string) file_get_contents($root . '/app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('data-adventuring-sheet', $view);
        self::assertStringContainsString('Core adventuring measures', $view);
        self::assertStringContainsString('data-vital-measures-form', $view);
        self::assertStringContainsString('<h2>Attacks</h2>', $view);
        self::assertStringContainsString('<h2>Spellcasting</h2>', $view);
        self::assertStringContainsString('<h2>Equipment</h2>', $view);
        self::assertStringContainsString('<h2>Saving Throws</h2>', $view);
        self::assertStringContainsString('<h2>Skills</h2>', $view);
        self::assertStringContainsString("'gmrc-adventuring-sheet'", $frontend);
        self::assertStringContainsString('@media(max-width:760px)', $css);
    }
}
