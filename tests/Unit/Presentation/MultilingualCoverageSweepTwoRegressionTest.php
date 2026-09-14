<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class MultilingualCoverageSweepTwoRegressionTest extends TestCase
{
    public function test_character_ledger_exposes_additional_visible_copy_to_gettext(): void
    {
        $root = dirname(__DIR__, 3);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString("esc_html_e('Registered Adventurer', 'great-marketrealm-companion')", $view);
        self::assertStringContainsString("esc_html_e('Adventuring Measures', 'great-marketrealm-companion')", $view);
        self::assertStringContainsString("esc_attr__('Pack load summary', 'great-marketrealm-companion')", $view);
    }

    public function test_dutch_and_german_catalogues_cover_the_new_ledger_strings(): void
    {
        $root = dirname(__DIR__, 3);
        $dutch = file_get_contents($root . '/languages/great-marketrealm-companion-nl_NL.po');
        $german = file_get_contents($root . '/languages/great-marketrealm-companion-de_DE.po');

        self::assertIsString($dutch);
        self::assertIsString($german);
        self::assertStringContainsString("msgid \"Registered Adventurer\"\nmsgstr \"Geregistreerde avonturier\"", $dutch);
        self::assertStringContainsString("msgid \"Registered Adventurer\"\nmsgstr \"Registrierter Abenteurer\"", $german);
        self::assertStringContainsString("msgid \"The Adventurer’s Pack is empty.\"", $german);
    }
}
