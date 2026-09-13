<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class FullInterfaceTranslationSweepRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_character_ledger_primary_actions_and_combat_notes_are_translatable(): void
    {
        $view = file_get_contents($this->root . '/app/Modules/Characters/Views/show.php');
        self::assertIsString($view);
        self::assertStringContainsString("__('Return to Register', 'great-marketrealm-companion')", $view);
        self::assertStringContainsString("esc_html_e('How the Guild Counts', 'great-marketrealm-companion')", $view);
        self::assertStringContainsString("esc_html_e('Attack roll', 'great-marketrealm-companion')", $view);
    }

    public function test_complete_adventurer_banner_is_translatable(): void
    {
        $source = file_get_contents($this->root . '/app/Modules/Characters/Services/CompleteAdventurerPresenter.php');
        self::assertIsString($source);
        self::assertStringContainsString("__('Complete Adventurer', 'great-marketrealm-companion')", $source);
        self::assertStringContainsString("__('Every major Guild folio is connected to this adventurer.', 'great-marketrealm-companion')", $source);
    }

    public function test_dutch_catalogue_contains_the_new_ledger_strings(): void
    {
        $po = file_get_contents($this->root . '/languages/great-marketrealm-companion-nl_NL.po');
        self::assertIsString($po);
        self::assertStringContainsString('msgid "How the Guild Counts"', $po);
        self::assertStringContainsString('msgstr "Hoe het Gilde telt"', $po);
    }
}
