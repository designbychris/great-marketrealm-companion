<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class GateSecurityRegressionTest extends TestCase
{
    public function testStewardCanConfigureTurnstileWithoutRenderingSavedSecret(): void
    {
        $provider=$this->source('app/Providers/AdministrationServiceProvider.php');$view=$this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString("admin_post_gmrc_save_gate_security",$provider);self::assertStringContainsString("check_admin_referer('gmrc_save_gate_security'",$provider);self::assertStringContainsString('type="password"',$view);self::assertStringNotContainsString("['secret_key'] ?? '')); ?>\"",$view);
    }
    public function testGateSecuritySettingsKeepSecretServerSideAndAllowBlankPreservation(): void
    {
        $source=$this->source('app/Modules/Administration/Security/GateSecuritySettings.php');self::assertStringContainsString("OPTION_NAME = 'gmrc_gate_security'",$source);self::assertStringContainsString("trim(\$secretKey) !== ''",$source);self::assertStringContainsString("\$current['secret_key']",$source);
    }
    public function testGuildGatePerformsServerSideTurnstileVerification(): void
    {
        $verifier=$this->source('app/Modules/GuildGate/Services/TurnstileVerifier.php');$controller=$this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        self::assertStringContainsString('https://challenges.cloudflare.com/turnstile/v0/siteverify',$verifier);self::assertStringContainsString('wp_remote_post',$verifier);self::assertStringContainsString("verifyGateSecurity('login')",$controller);self::assertStringContainsString("verifyGateSecurity('register')",$controller);
    }
    public function testTurnstileWidgetAndScriptOnlyAppearWhenConfigured(): void
    {
        $provider=$this->source('app/Modules/GuildGate/GuildGateServiceProvider.php');$view=$this->source('app/Modules/GuildGate/Views/index.php');self::assertStringContainsString('gmrc-cloudflare-turnstile',$provider);self::assertStringContainsString('cf-turnstile',$view);self::assertStringContainsString('turnstileConfigured',$view);
    }
    public function testRegistrationAndLoginCanBeProtectedIndependently(): void
    {
        $settings=$this->source('app/Modules/Administration/Security/GateSecuritySettings.php');$view=$this->source('app/Modules/Administration/Views/stewards-office.php');self::assertStringContainsString('protect_registration',$settings);self::assertStringContainsString('protect_login',$settings);self::assertStringContainsString('protect_registration',$view);self::assertStringContainsString('protect_login',$view);
    }
    private function source(string $path): string { $source=file_get_contents(dirname(__DIR__,4).'/'.$path);self::assertIsString($source);return $source; }
}
