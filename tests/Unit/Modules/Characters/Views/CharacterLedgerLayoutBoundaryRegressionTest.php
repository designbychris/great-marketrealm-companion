<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class CharacterLedgerLayoutBoundaryRegressionTest extends TestCase
{
    public function testOpenLedgerHasExplicitTerminalLayoutBoundary(): void
    {
        $view = $this->view();

        self::assertStringContainsString(
            'class="gmrc-open-ledger"',
            $view
        );
        self::assertStringContainsString(
            'data-character-ledger-boundary',
            $view
        );
    }

    public function testLedgerBoundaryPreservesSpaceBeforeThemeFooter(): void
    {
        $css = $this->characterCss();

        self::assertStringContainsString(
            '.gmrc-open-ledger[data-character-ledger-boundary]',
            $css
        );
        self::assertStringContainsString(
            'margin-bottom:',
            $css
        );
        self::assertStringContainsString(
            'clamp(',
            $css
        );
        self::assertStringContainsString(
            '6.5rem',
            $css
        );
    }

    public function testLedgerBoundaryEstablishesIndependentLayoutContext(): void
    {
        $css = $this->characterCss();

        self::assertStringContainsString(
            'clear: both;',
            $css
        );
        self::assertStringContainsString(
            'isolation: isolate;',
            $css
        );
        self::assertStringContainsString(
            'data-character-ledger-boundary]::after',
            $css
        );
    }

    public function testCollapsedTabsDoNotRemoveLedgerBoundary(): void
    {
        $css = $this->characterCss();

        self::assertStringContainsString(
            '.gmrc-ledger-tabpanel:not([hidden])',
            $css
        );
        self::assertStringContainsString(
            'position: relative;',
            $css
        );
    }

    public function testLayoutRepairRemainsResponsive(): void
    {
        $css = $this->characterCss();

        self::assertStringContainsString(
            '@media (max-width: 820px)',
            $css
        );
        self::assertStringContainsString(
            '4.5rem',
            $css
        );
    }

    public function testFellowshipStylesDoNotTargetOpenLedgerBoundary(): void
    {
        $root = $this->root();
        $fellowshipCss = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($fellowshipCss);
        self::assertStringNotContainsString(
            '.gmrc-open-ledger',
            $fellowshipCss
        );
        self::assertStringNotContainsString(
            '[data-character-ledger-boundary]',
            $fellowshipCss
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

    public function testPurseFormsRemainOutsideAnyOtherFormBoundary(): void
    {
        $view = $this->view();

        $purse = strpos(
            $view,
            'class="gmrc-adventurer-purse"'
        );
        $firstPurseForm = strpos(
            $view,
            'class="gmrc-adventurer-purse__form"',
            $purse
        );

        self::assertIsInt($purse);
        self::assertIsInt($firstPurseForm);

        $before = substr(
            $view,
            0,
            $firstPurseForm
        );

        preg_match_all(
            '/<form\b/i',
            $before,
            $opening
        );
        preg_match_all(
            '/<\/form>/i',
            $before,
            $closing
        );

        self::assertSame(
            count($opening[0]),
            count($closing[0])
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
