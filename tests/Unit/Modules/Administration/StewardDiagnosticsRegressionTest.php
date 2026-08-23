<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardDiagnosticsRegressionTest extends TestCase
{
    public function testAdministrationProviderRegistersSettingsAndDiagnosticsServices(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('CompanionSettings::class', $provider);
        self::assertStringContainsString('StewardDiagnostics::class', $provider);
    }

    public function testCompanionSettingsAreAdministratorNonceProtected(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('admin_post_gmrc_save_companion_settings', $provider);
        self::assertStringContainsString("check_admin_referer('gmrc_save_companion_settings'", $provider);
        self::assertStringContainsString('current_user_can(self::CAPABILITY)', $provider);
    }

    public function testDiagnosticsDistinguishHealthyAttentionAndInformationalStates(): void
    {
        $source = $this->source('app/Modules/Administration/Diagnostics/StewardDiagnostics.php');
        self::assertStringContainsString("'healthy'", $source);
        self::assertStringContainsString("'attention'", $source);
        self::assertStringContainsString("'info'", $source);
        self::assertStringContainsString('The Companion is in good order.', $source);
    }

    public function testDiagnosticsCoverRuntimeUploadsHttpAndGateSecurity(): void
    {
        $source = $this->source('app/Modules/Administration/Diagnostics/StewardDiagnostics.php');
        self::assertStringContainsString('wp_upload_dir', $source);
        self::assertStringContainsString('wp_remote_post', $source);
        self::assertStringContainsString('Cloudflare Turnstile', $source);
        self::assertStringContainsString('Registration protection', $source);
        self::assertStringContainsString('Login protection', $source);
    }

    public function testStewardsOfficeRendersHealthSealEnvironmentAndSettingsForm(): void
    {
        $view = $this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('Steward Diagnostics', $view);
        self::assertStringContainsString('System Diagnostics', $view);
        self::assertStringContainsString('Environment', $view);
        self::assertStringContainsString('gmrc_save_companion_settings', $view);
        self::assertStringContainsString('Steward contact email', $view);
    }

    public function testDiagnosticsNeverRenderTurnstileSecret(): void
    {
        $view = $this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringNotContainsString('$gateSecurity[\'secret_key\']', $view);
        self::assertStringContainsString('type="password"', $view);
    }

    public function testAdministrationStylesIncludeResponsiveAndForcedColourDiagnostics(): void
    {
        $css = $this->source('assets/css/admin.css');
        self::assertStringContainsString('.gmrc-steward-health', $css);
        self::assertStringContainsString('.gmrc-diagnostic--healthy', $css);
        self::assertStringContainsString('@media(max-width:782px)', $css);
        self::assertStringContainsString('@media(forced-colors:active)', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
