<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\BridgeSeal;

use PHPUnit\Framework\TestCase;

final class ConnectedCoinFlowPresentationRegressionTest extends TestCase
{
    public function testCharacterTransferIsPresentedAsPrimaryCoinFlow(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'Coin Between Companions',
            $view
        );
        self::assertStringContainsString(
            'updates both purses',
            $view
        );
        self::assertStringContainsString(
            'Move Coin Between Purses',
            $view
        );
        self::assertStringContainsString(
            "'/parties/{id}/treasury/transfer'",
            $this->routes()
        );
    }

    public function testOrdinaryTreasuryControlsAreExplicitlyCompanyOnly(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'Company-only Treasury adjustments',
            $view
        );
        self::assertStringContainsString(
            'These controls do not change a Character’s purse.',
            $view
        );
        self::assertStringContainsString(
            'Record External Income',
            $view
        );
        self::assertStringContainsString(
            'Record Company Expense',
            $view
        );
    }

    public function testCompanyOnlyControlsAreCollapsedByDefault(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            '<details class="gmrc-fellowship-treasury-adjustments">',
            $view
        );
        self::assertStringNotContainsString(
            '<details open class="gmrc-fellowship-treasury-adjustments">',
            $view
        );
    }

    public function testBothTreasuryAccountingRoutesRemainAvailable(): void
    {
        $routes = $this->routes();

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

    public function testConnectedTransferStillDisplaysMemberPersonalPurse(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            "'%s — purse %s'",
            $view
        );
        self::assertStringContainsString(
            '->purse()',
            $view
        );
        self::assertStringContainsString(
            '->formatted()',
            $view
        );
    }

    private function view(): string
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($source);

        return $source;
    }

    private function routes(): string
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Routes.php'
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
