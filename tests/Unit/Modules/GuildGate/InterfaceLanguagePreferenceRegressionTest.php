<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class InterfaceLanguagePreferenceRegressionTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 4);
    }

    public function test_profile_exposes_system_english_dutch_german_and_spanish_language_choices(): void
    {
        $profile = file_get_contents($this->root() . '/app/Modules/GuildGate/GuildProfile.php');
        $view = file_get_contents($this->root() . '/app/Modules/GuildGate/Views/profile.php');

        self::assertIsString($profile);
        self::assertStringContainsString("'en_GB' => 'English (UK)'", $profile);
        self::assertStringContainsString("'nl_NL' => 'Nederlands'", $profile);
        self::assertStringContainsString("'de_DE' => 'Deutsch'", $profile);
        self::assertStringContainsString("'es_ES' => 'Español'", $profile);
        self::assertStringContainsString('name="interface_locale"', $view);
    }

    public function test_preference_is_persisted_as_shared_user_meta(): void
    {
        $service = file_get_contents($this->root() . '/app/Modules/GuildGate/Services/UpdateGuildProfile.php');
        $profile = file_get_contents($this->root() . '/app/Modules/GuildGate/GuildProfile.php');

        self::assertIsString($service);
        self::assertStringContainsString("INTERFACE_LOCALE_META = 'gmrc_interface_locale'", $profile);
        self::assertStringContainsString("['', 'en_GB', 'nl_NL', 'de_DE', 'es_ES']", $service);
        self::assertStringContainsString('delete_user_meta', $service);
        self::assertStringContainsString('update_user_meta', $service);
    }

    public function test_companion_resolves_user_locale_before_loading_textdomain(): void
    {
        $plugin = file_get_contents($this->root() . '/great-marketrealm-companion.php');

        self::assertIsString($plugin);
        self::assertStringContainsString("add_filter(\n    'determine_locale'", $plugin);
        self::assertStringContainsString("get_user_meta(\$userId, 'gmrc_interface_locale', true)", $plugin);
        self::assertStringContainsString("['en_GB', 'nl_NL', 'de_DE', 'es_ES']", $plugin);
    }
}
