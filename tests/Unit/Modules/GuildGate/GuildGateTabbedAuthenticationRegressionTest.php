<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class GuildGateTabbedAuthenticationRegressionTest extends TestCase
{
    public function testGateUsesAccessibleTabsAndPanels(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/index.php');

        self::assertStringContainsString('role="tablist"', $view);
        self::assertStringContainsString('role="tab"', $view);
        self::assertStringContainsString('role="tabpanel"', $view);
        self::assertStringContainsString('aria-controls="guild-gate-login"', $view);
        self::assertStringContainsString('aria-controls="guild-gate-register"', $view);
        self::assertStringContainsString('data-guild-gate-tab="login"', $view);
        self::assertStringContainsString('data-guild-gate-tab="register"', $view);
    }

    public function testGateIntentSupportsDeepLinksAndValidationRecovery(): void
    {
        $controller = $this->source(
            'app/Modules/GuildGate/Controllers/GuildGateController.php'
        );

        self::assertStringContainsString("'gateIntent' => \$this->gateIntent()", $controller);
        self::assertStringContainsString("old('gate_intent', '')", $controller);
        self::assertStringContainsString("string('gate', '')", $controller);
        self::assertStringContainsString("return \$intent === 'register' ? 'register' : 'login';", $controller);
    }

    public function testTabLinksRemainFunctionalWithoutJavascript(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/index.php');

        self::assertStringContainsString("'gate' => \$tab", $view);
        self::assertStringContainsString("'return_route' => \$returnRoute", $view);
        self::assertStringContainsString("\$gateTabUrl('login')", $view);
        self::assertStringContainsString("\$gateTabUrl('register')", $view);
    }

    public function testKeyboardControllerAndUrlStateAreEnqueued(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $script = $this->source(
            'assets/js/modules/guild-gate/guild-gate-tabs.js'
        );

        self::assertStringContainsString('gmrc-guild-gate-tabs', $frontend);
        self::assertStringContainsString(
            'assets/js/modules/guild-gate/guild-gate-tabs.js',
            $frontend
        );
        self::assertStringContainsString("event.key === 'ArrowRight'", $script);
        self::assertStringContainsString("event.key === 'ArrowLeft'", $script);
        self::assertStringContainsString("event.key === 'Home'", $script);
        self::assertStringContainsString("event.key === 'End'", $script);
        self::assertStringContainsString('new URL(tab.href, window.location.href)', $script);
        self::assertStringContainsString("panel.hidden =", $script);
    }

    public function testTurnstileContractsRemainInsideTheirOwnForms(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/index.php');

        self::assertStringContainsString("protect_login", $view);
        self::assertStringContainsString("protect_registration", $view);
        self::assertSame(2, substr_count($view, 'class="cf-turnstile"'));
        self::assertStringContainsString("gmrc_guild_gate_login", $view);
        self::assertStringContainsString("gmrc_guild_gate_register", $view);
    }

    public function testTabbedTreatmentKeepsAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-gate.css');

        self::assertStringContainsString('.gmrc-guild-gate__folio[hidden]', $css);
        self::assertStringContainsString('[aria-selected="true"]', $css);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString('@media (max-width: 720px)', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);

        self::assertIsString($source);

        return $source;
    }
}
