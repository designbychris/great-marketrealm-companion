<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class GuildAccountIntegrationRegressionTest extends TestCase
{
    public function testSidebarUsesCanonicalGuildProfileAccountType(): void
    {
        $sidebar = $this->source('app/Core/View/Templates/components/sidebar.php');
        self::assertStringContainsString('GuildProfile::accountType(', $sidebar);
        self::assertStringContainsString('AccountType::label(', $sidebar);
        self::assertStringNotContainsString("in_array(\n    'gmrc_dm'", $sidebar);
    }

    public function testSidebarIdentityLinksToGuildAccount(): void
    {
        $sidebar = $this->source('app/Core/View/Templates/components/sidebar.php');
        self::assertStringContainsString("'guild-profile'", $sidebar);
        self::assertStringContainsString('gmrc-sidebar__account-link', $sidebar);
        self::assertStringContainsString('aria-label="Open Guild Account"', $sidebar);
        self::assertStringContainsString('wp_logout_url($homeUrl)', $sidebar);
    }

    public function testProfileDelegatesPasswordManagementToWordPress(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        self::assertStringContainsString('wp_lostpassword_url($this->profileUrl())', $controller);
        self::assertStringContainsString('Manage password', $view);
        self::assertStringNotContainsString('new_password', $view);
        self::assertStringNotContainsString('password_confirmation', $view);
    }

    public function testProfileProvidesExplicitSecureLogoutBackToGate(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        self::assertStringContainsString('wp_logout_url($this->gateUrl())', $controller);
        self::assertStringContainsString('Sign out of the Companion', $view);
    }

    public function testAccountSecurityShowsRoleAwareEffectiveAccess(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        self::assertStringContainsString('Account &amp; security', $view);
        self::assertStringContainsString('Companion + Dungeon Master tools', $view);
        self::assertStringContainsString('Companion player tools', $view);
        self::assertStringContainsString('Changing profile details never changes Player or Dungeon Master permissions.', $view);
    }

    public function testAccountSecurityStylesHaveResponsiveAndForcedColourCoverage(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-profile.css');
        self::assertStringContainsString('.gmrc-guild-profile__security', $css);
        self::assertStringContainsString('.gmrc-guild-profile__security-actions', $css);
        self::assertStringContainsString('@media(max-width:760px)', $css);
        self::assertStringContainsString('@media(forced-colors:active)', $css);
    }

    public function testAdministratorsReceivePlayerAndDmCompanionAccess(): void
    {
        $roles = $this->source(
            'app/Modules/GuildGate/Services/GuildRoleRegistrar.php'
        );

        self::assertStringContainsString("\$administrator->add_cap(self::ACCESS)", $roles);
        self::assertStringContainsString(
            "\$administrator->add_cap(self::MANAGE_CAMPAIGNS)",
            $roles
        );
    }

    public function testWordPressAdminBarIsReservedForAdministrators(): void
    {
        $provider = $this->source(
            'app/Modules/GuildGate/GuildGateServiceProvider.php'
        );
        $policy = $this->source(
            'app/Modules/GuildGate/Services/GuildAdminBarVisibility.php'
        );

        self::assertStringContainsString("'show_admin_bar'", $provider);
        self::assertStringContainsString('GuildAdminBarVisibility::class', $provider);
        self::assertStringContainsString("current_user_can('manage_options')", $policy);
        self::assertStringContainsString('? $show', $policy);
        self::assertStringContainsString(': false', $policy);
    }

    public function testDocumentationClosesGuildAccountIntegrationSlice(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/GuildGatePhase314.md');
        self::assertStringContainsString('Phase III.14.2 — Guild Account & Gate Integration', $docs);
        self::assertStringContainsString('canonical signed-in account destination', $docs);
        self::assertStringContainsString("WordPress's native lost-password/reset flow", $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
