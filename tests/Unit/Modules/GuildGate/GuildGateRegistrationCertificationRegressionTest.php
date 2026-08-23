<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class GuildGateRegistrationCertificationRegressionTest extends TestCase
{
    public function testRegistrationRejectionsReturnToJoinGuildTabWithSafeAuditTrail(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $audit = $this->source('app/Modules/GuildGate/Services/GuildGateAudit.php');

        self::assertStringContainsString("'registration_started'", $controller);
        self::assertStringContainsString("'registration_rejected'", $controller);
        self::assertStringContainsString("->gateTabUrl('register')", $controller);
        self::assertStringContainsString("'[GMRC Guild Gate] '", $audit);
        self::assertStringNotContainsString('password', $audit);
        self::assertStringNotContainsString('cf-turnstile-response', $audit);
    }

    public function testTabbedGateUsesExplicitTurnstileRenderingPerActiveForm(): void
    {
        $provider = $this->source('app/Modules/GuildGate/GuildGateServiceProvider.php');
        $view = $this->source('app/Modules/GuildGate/Views/index.php');
        $script = $this->source('assets/js/modules/guild-gate/guild-gate-tabs.js');

        self::assertStringContainsString('api.js?render=explicit', $provider);
        self::assertStringContainsString('data-gmrc-turnstile', $view);
        self::assertStringContainsString('data-action="gmrc_register"', $view);
        self::assertStringContainsString('data-action="gmrc_login"', $view);
        self::assertStringContainsString('window.turnstile.render', $script);
        self::assertStringContainsString('renderWhenReady(panel)', $script);
    }

    public function testTurnstileServerValidationBindsTokenToExpectedGateIntent(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $verifier = $this->source('app/Modules/GuildGate/Services/TurnstileVerifier.php');

        self::assertStringContainsString("? 'gmrc_login' : 'gmrc_register'", $controller);
        self::assertStringContainsString('$expectedAction', $verifier);
        self::assertStringContainsString('(string) ($decoded[\'action\'] ?? \'\')', $verifier);
    }

    public function testCreatedGuildAccountIsReopenedAndRoleIsCertified(): void
    {
        $registration = $this->source('app/Modules/GuildGate/Services/RegisterGuildMember.php');

        self::assertStringContainsString('get_userdata((int) $userId)', $registration);
        self::assertStringContainsString('AccountType::role($accountType)', $registration);
        self::assertStringContainsString('$user->set_role($role)', $registration);
        self::assertStringContainsString('GuildProfile::ACCOUNT_TYPE_META', $registration);
    }


    public function testRegistrationUsesDedicatedWordPressAdminPostGateway(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $view = $this->source('app/Modules/GuildGate/Views/index.php');

        self::assertStringContainsString("'admin_post_gmrc_guild_gate_register'", $frontend);
        self::assertStringContainsString("'admin_post_nopriv_gmrc_guild_gate_register'", $frontend);
        self::assertStringContainsString('handleGuildGateRegistration', $frontend);
        self::assertStringContainsString("'registration_gateway_received'", $frontend);
        self::assertStringContainsString("'application_gateway_dispatching'", $frontend);
        self::assertStringContainsString('value="gmrc_guild_gate_register"', $view);
        self::assertStringContainsString('value="gmrc_app_request"', $view);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
