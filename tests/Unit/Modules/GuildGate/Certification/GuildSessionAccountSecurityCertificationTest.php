<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate\Certification;

use PHPUnit\Framework\TestCase;

final class GuildSessionAccountSecurityCertificationTest extends TestCase
{
    public function testCompanionAdmissionRequiresTheGuildAccessCapability(): void
    {
        $policy = $this->source('app/Modules/GuildGate/Services/GuildAccessPolicy.php');

        self::assertStringContainsString('is_user_logged_in()', $policy);
        self::assertStringContainsString('get_current_user_id()', $policy);
        self::assertStringContainsString('GuildRoleRegistrar::ACCESS', $policy);
        self::assertStringContainsString('user_can($userId, GuildRoleRegistrar::ACCESS)', $policy);
    }

    public function testGuildAccessPolicyIsRegisteredAsASharedService(): void
    {
        $provider = $this->source('app/Modules/GuildGate/GuildGateServiceProvider.php');

        self::assertStringContainsString('GuildAccessPolicy::class', $provider);
        self::assertStringContainsString('$this->app->singleton(GuildAccessPolicy::class);', $provider);
    }

    public function testValidWordpressCredentialsWithoutGuildAccessAreRejected(): void
    {
        $auth = $this->source('app/Modules/GuildGate/Services/AuthenticateGuildMember.php');

        self::assertStringContainsString('private GuildAccessPolicy $access', $auth);
        self::assertStringContainsString('! $this->access->allows((int) $user->ID)', $auth);
        self::assertStringContainsString('wp_clear_auth_cookie();', $auth);
        self::assertStringContainsString('wp_set_current_user(0);', $auth);
        self::assertStringContainsString('not registered for the Great Marketrealm Companion', $auth);
    }

    public function testAnonymousVisitorsStillReceiveThePublicGuildGate(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('if (! is_user_logged_in())', $frontend);
        self::assertStringContainsString('return $gate->show();', $frontend);
    }

    public function testLoggedInWordpressAccountsWithoutGuildAccessReceiveAdmissionRefusal(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $view = $this->source('app/Modules/GuildGate/Views/access-denied.php');

        self::assertStringContainsString('GuildAccessPolicy::class)->allowsCurrentUser()', $frontend);
        self::assertStringContainsString('return $gate->accessDenied();', $frontend);
        self::assertStringContainsString('public function accessDenied(): string', $controller);
        self::assertStringContainsString('Guild papers required', $view);
        self::assertStringContainsString('does not currently hold Companion access', $view);
    }

    public function testAdmissionRefusalOffersNonceProtectedWordpressLogout(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $view = $this->source('app/Modules/GuildGate/Views/access-denied.php');

        self::assertStringContainsString("'logoutUrl' => wp_logout_url(\$this->gateUrl())", $controller);
        self::assertStringContainsString('Sign out and return to the Guild Gate', $view);
        self::assertStringNotContainsString('wp_logout();', $view);
    }

    public function testPrivateApplicationCommandsRequireCertifiedGuildAdmission(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('! $publicGuildGateRoute', $frontend);
        self::assertStringContainsString('! $this->app->make(GuildAccessPolicy::class)->allowsCurrentUser()', $frontend);
        self::assertStringContainsString("['guild-gate/login', 'guild-gate/register']", $frontend);
    }

    public function testPublicLoginAndRegistrationRemainAvailableWithoutASession(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString("['guild-gate/login', 'guild-gate/register']", $frontend);
        self::assertStringContainsString("'gmrc_guild_gate_login'", $frontend);
        self::assertStringContainsString("'gmrc_guild_gate_register'", $frontend);
        self::assertStringContainsString('handleGuildGateRegistration()', $frontend);
    }

    public function testUnauthorizedSessionsOnlyReceiveGuildGateAssets(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $policy = strpos($frontend, "if (! \$this->app->make(GuildAccessPolicy::class)->allowsCurrentUser()) {");
        $gateAssets = strpos($frontend, '$this->enqueueGuildGate();', $policy === false ? 0 : $policy);
        $components = strpos($frontend, '$this->enqueueComponents();', $policy === false ? 0 : $policy);

        self::assertIsInt($policy);
        self::assertIsInt($gateAssets);
        self::assertIsInt($components);
        self::assertLessThan($components, $gateAssets);
    }

    public function testRegisteredRolesAndAdministratorRetainTheAdmissionCapability(): void
    {
        $roles = $this->source('app/Modules/GuildGate/Services/GuildRoleRegistrar.php');

        self::assertStringContainsString("add_role('gmrc_player'", $roles);
        self::assertStringContainsString("add_role('gmrc_dm'", $roles);
        self::assertStringContainsString('self::ACCESS => true', $roles);
        self::assertStringContainsString('$administrator->add_cap(self::ACCESS);', $roles);
    }

    public function testDeniedSessionTreatmentRetainsAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-gate.css');

        self::assertStringContainsString('.gmrc-guild-gate--access-denied', $css);
        self::assertStringContainsString('.gmrc-guild-gate__access-action:focus-visible', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 5) . '/' . $path);
        self::assertIsString($source);

        return $source;
    }
}
