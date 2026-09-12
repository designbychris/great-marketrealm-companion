<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class InterfaceTranslationAuditRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_live_and_printable_character_sheets_use_the_companion_text_domain(): void
    {
        foreach ([
            '/app/Modules/Characters/Views/adventuring-sheet.php',
            '/app/Modules/Characters/Views/printable-sheet.php',
            '/app/Modules/Characters/Views/create.php',
            '/app/Modules/Characters/Views/edit.php',
        ] as $relative) {
            $source = file_get_contents($this->root . $relative);
            self::assertIsString($source);
            self::assertStringContainsString('great-marketrealm-companion', $source);
        }
    }

    public function test_browser_generated_character_copy_is_localized_from_php(): void
    {
        $provider = file_get_contents($this->root . '/app/Providers/FrontendServiceProvider.php');
        self::assertIsString($provider);
        self::assertStringContainsString("'gmrc-adventuring-sheet',\n            'gmrcAdventuringSheetI18n'", $provider);
        self::assertStringContainsString("'gmrc-printable-sheet',\n            'gmrcPrintableSheetI18n'", $provider);
        self::assertStringContainsString("'gmrc-guild-dice',\n            'gmrcGuildDiceI18n'", $provider);
        self::assertStringContainsString("'gmrc-complete-registration',\n            'gmrcRegistrationI18n'", $provider);
        self::assertStringContainsString("'gmrc-dice-of-destiny',\n            'gmrcDiceOfDestinyI18n'", $provider);
    }

    public function test_audit_documents_interface_and_content_boundary(): void
    {
        $guide = file_get_contents($this->root . '/docs/Interface-Translation-Audit.md');
        self::assertIsString($guide);
        self::assertStringContainsString('canonical MarketRealm authored content', $guide);
        self::assertStringContainsString('wp_localize_script()', $guide);
    }
}
