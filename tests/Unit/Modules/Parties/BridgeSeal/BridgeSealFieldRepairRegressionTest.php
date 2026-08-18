<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\BridgeSeal;

use PHPUnit\Framework\TestCase;

final class BridgeSealFieldRepairRegressionTest extends TestCase
{
    public function testAdminPostDispatchesPostedRouteDirectlyThroughRouter(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            '->make(Router::class)',
            $source
        );
        self::assertStringContainsString(
            '->dispatch(',
            $source
        );
        self::assertStringContainsString(
            '$methodOverride',
            $source
        );
        self::assertStringContainsString(
            "'/' . trim(\$route, '/')",
            $source
        );
    }

    public function testAdminPostCommandFlowRequiresExplicitResponse(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'if ($result instanceof Response)',
            $source
        );
        self::assertStringContainsString(
            '$result->send();',
            $source
        );
        self::assertStringContainsString(
            "wp_safe_redirect(\n"
            . "            home_url('/companion/')",
            $source
        );
    }

    public function testPurseRoutesRemainRegisteredForAdminPostCommands(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/characters/{id}/purse/deposit'",
            $routes
        );
        self::assertStringContainsString(
            "'/characters/{id}/purse/withdraw'",
            $routes
        );
    }

    public function testFellowshipTreasuryRoutesRemainRegistered(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/parties/{id}/treasury/deposit'",
            $routes
        );
        self::assertStringContainsString(
            "'/parties/{id}/treasury/withdraw'",
            $routes
        );
        self::assertStringContainsString(
            "'/parties/{id}/treasury/transfer'",
            $routes
        );
    }

    public function testCompactPortraitStripsGeneratedBackgroundAndFrame(): void
    {
        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            'data-fellowship-variant="compact"',
            $css
        );
        self::assertStringContainsString(
            '[data-portrait-layer="background"]',
            $css
        );
        self::assertStringContainsString(
            '[data-portrait-layer="frame"]',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-g2-background',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-g2-frame',
            $css
        );
    }

    public function testCompactPortraitCanvasIsBorderless(): void
    {
        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-fellowship-portrait__canvas',
            $css
        );
        self::assertStringContainsString(
            'border: 0;',
            $css
        );
        self::assertStringContainsString(
            'background: transparent;',
            $css
        );
        self::assertStringContainsString(
            'box-shadow: none;',
            $css
        );
    }

    public function testFellowshipIndexStillUsesCompactPortraitVariant(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/index.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            "'variant' => 'compact'",
            $view
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
