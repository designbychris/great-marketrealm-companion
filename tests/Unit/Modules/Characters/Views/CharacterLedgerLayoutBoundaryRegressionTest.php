<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

/**
 * Phase III.11.3C.2
 *
 * Protects the repaired Character Ledger shell after the temporary C.1
 * layout-boundary workaround was retired.
 */
final class CharacterLedgerLayoutBoundaryRegressionTest extends TestCase
{
    public function testTemporaryLedgerBoundaryAttributeIsRetired(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'class="gmrc-open-ledger"',
            $view
        );

        self::assertStringNotContainsString(
            'data-character-ledger-boundary',
            $view
        );
    }

    public function testTemporaryFooterSpacingWorkaroundIsRetired(): void
    {
        $css = $this->characterCss();

        self::assertStringNotContainsString(
            '.gmrc-open-ledger[data-character-ledger-boundary]',
            $css
        );

        self::assertStringNotContainsString(
            'Phase III.11.3C.1 — Ledger Layout Repair',
            $css
        );
    }

    public function testTemporaryFloatClearWorkaroundIsRetired(): void
    {
        $css = $this->characterCss();

        self::assertStringNotContainsString(
            'data-character-ledger-boundary]::after',
            $css
        );
    }

    public function testLivingLedgerRootRemainsIntact(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'data-living-ledger',
            $view
        );

        self::assertStringContainsString(
            'data-character-id=',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-open-ledger-title"',
            $view
        );
    }

    public function testLedgerTabPanelsRetainTheirNativeHiddenContract(): void
    {
        $css = $this->characterCss();

        self::assertStringContainsString(
            '.gmrc-ledger-tabpanel[hidden]',
            $css
        );

        self::assertStringContainsString(
            'display: none !important;',
            $css
        );
    }

    public function testCharacterViewBlockElementsRemainBalanced(): void
    {
        $view = $this->view();

        foreach ([
            'section',
            'div',
            'article',
            'aside',
            'form',
            'nav',
            'header',
        ] as $tag) {
            preg_match_all(
                '/<' . $tag . '\b/i',
                $view,
                $opening
            );

            preg_match_all(
                '/<\/' . $tag . '>/i',
                $view,
                $closing
            );

            self::assertSame(
                count($opening[0]),
                count($closing[0]),
                sprintf(
                    'Expected <%s> elements to remain balanced.',
                    $tag
                )
            );
        }
    }

    public function testFellowshipStylesDoNotOwnCharacterLedgerShell(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($source);

        self::assertStringNotContainsString(
            'data-character-ledger-boundary',
            $source
        );

        self::assertStringNotContainsString(
            '.gmrc-open-ledger[data-character-ledger-boundary]',
            $source
        );
    }

    public function testLedgerRepairNowLivesAtRuntimeContractBoundary(): void
    {
        $role = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Models/ValueObjects/'
            . 'PartyMembershipRole.php'
        );

        $view = $this->view();

        self::assertIsString($role);

        self::assertStringContainsString(
            'public function label(): string',
            $role
        );

        self::assertStringContainsString(
            "->role()\n"
            . "                                                    ->label()",
            $view
        );
    }

    private function view(): string
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($source);

        return $source;
    }

    private function characterCss(): string
    {
        $source = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'open-ledger.css'
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
