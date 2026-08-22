<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class GuildGateRegressionTest extends TestCase
{
    public function testGuildGateIsInstalledWithSignedInProfileNavigation(): void
    {
        $registry = $this->source('app/Providers/KingdomServiceProvider.php');
        $kingdom = $this->source('app/Kingdoms/GuildGateKingdom.php');

        self::assertStringContainsString('new GuildGateKingdom($this->app)', $registry);
        self::assertStringContainsString("return 'guild-gate';", $kingdom);
        self::assertStringContainsString('GuildGateServiceProvider::class', $kingdom);
        self::assertStringContainsString("'Guild Profile'", $kingdom);
        self::assertStringContainsString("'guild-profile'", $kingdom);
    }

    public function testGateRoutesUseExistingApplicationRouter(): void
    {
        $routes = $this->source('app/Modules/GuildGate/Routes.php');

        self::assertStringContainsString("'/guild-gate/login'", $routes);
        self::assertStringContainsString("'/guild-gate/register'", $routes);
        self::assertStringContainsString("[GuildGateController::class, 'login']", $routes);
        self::assertStringContainsString("[GuildGateController::class, 'register']", $routes);
    }

    public function testLoggedOutCompanionRendersGuildGateInsteadOfApplicationShell(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('if (! is_user_logged_in())', $frontend);
        self::assertStringContainsString('->make(GuildGateController::class)', $frontend);
        self::assertStringContainsString('->show();', $frontend);
    }

    public function testOnlyPublicGateCommandsMayRunWhileLoggedOut(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString("['guild-gate/login', 'guild-gate/register']", $frontend);
        self::assertStringContainsString('! is_user_logged_in() && ! $publicGuildGateRoute', $frontend);
        self::assertStringContainsString("'gmrc_guild_gate_login'", $frontend);
        self::assertStringContainsString("'gmrc_guild_gate_register'", $frontend);
    }

    public function testRegistrationNeverAcceptsArbitraryWordPressRole(): void
    {
        $registration = $this->source(
            'app/Modules/GuildGate/Services/RegisterGuildMember.php'
        );

        self::assertStringContainsString("'role' => AccountType::role(\$accountType)", $registration);
        self::assertStringContainsString('AccountType::values()', $registration);
        self::assertStringNotContainsString("\$_POST['role']", $registration);
    }

    public function testDmRoleDoesNotReceiveWordPressEditorialCapabilities(): void
    {
        $roles = $this->source(
            'app/Modules/GuildGate/Services/GuildRoleRegistrar.php'
        );

        self::assertStringContainsString("add_role('gmrc_dm'", $roles);
        self::assertStringContainsString("'gmrc_manage_campaigns'", $roles);
        self::assertStringNotContainsString("'edit_posts'", $roles);
        self::assertStringNotContainsString("'manage_options'", $roles);
        self::assertStringNotContainsString("'edit_users'", $roles);
    }

    public function testProfileContractReservesAccountAndPortraitMetadata(): void
    {
        $profile = $this->source('app/Modules/GuildGate/GuildProfile.php');

        self::assertStringContainsString("'gmrc_account_type'", $profile);
        self::assertStringContainsString("'gmrc_profile_portrait_attachment_id'", $profile);
    }

    public function testGateFormUsesNonceAndPasswordAutocompleteSemantics(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/index.php');

        self::assertStringContainsString("wp_nonce_field('gmrc_guild_gate_login'", $view);
        self::assertStringContainsString("wp_nonce_field('gmrc_guild_gate_register'", $view);
        self::assertStringContainsString('autocomplete="current-password"', $view);
        self::assertStringContainsString('autocomplete="new-password"', $view);
        self::assertStringContainsString('minlength="10"', $view);
        self::assertStringContainsString('wp_lostpassword_url(', $view);
    }

    public function testGateCssIncludesResponsiveAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-gate.css');

        self::assertStringContainsString('@media (max-width: 720px)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString('@media (prefers-reduced-transparency: reduce)', $css);
        self::assertStringContainsString(':focus-visible', $css);
    }

    public function testLogoutReturnsToCompanionGate(): void
    {
        $sidebar = $this->source(
            'app/Core/View/Templates/components/sidebar.php'
        );

        self::assertStringContainsString('wp_logout_url($homeUrl)', $sidebar);
        self::assertStringContainsString('GuildProfile::accountType(', $sidebar);
        self::assertStringContainsString('AccountType::label(', $sidebar);
        self::assertStringContainsString('GuildProfile::PORTRAIT_ATTACHMENT_META', $sidebar);
        self::assertStringContainsString('wp_get_attachment_image(', $sidebar);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(
            dirname(__DIR__, 4) . '/' . $path
        );

        self::assertIsString($source);

        return $source;
    }
}
