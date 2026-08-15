<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class LivingLedgerTabsRegressionTest extends TestCase
{
    public function testOpenLedgerProvidesAccessibleUpfrontIndex(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-living-ledger', $view);
        self::assertStringContainsString('role="tablist"', $view);
        self::assertStringContainsString('Overview', $view);
        self::assertStringContainsString('Skills & Training', $view);
        self::assertStringContainsString('Archive Notes', $view);
        self::assertStringContainsString('Spells & Abilities', $view);
        self::assertStringContainsString('Progression', $view);
        self::assertStringContainsString(
            'class="gmrc-ledger-index"',
            $view
        );
        self::assertStringContainsString(
            'Guild Ledger Index',
            $view
        );
        self::assertLessThan(
            strpos($view, 'id="gmrc-ledger-panel-overview"'),
            strpos($view, 'class="gmrc-ledger-index"')
        );
        /*
         * Count only the seven top-level Living Ledger panels.
         *
         * The Indexed Arcane Pantry has its own nested ARIA tabpanels, so a
         * global role="tabpanel" count would incorrectly treat those shelves
         * as additional Ledger pages.
         */
        self::assertSame(
            7,
            substr_count($view, 'data-ledger-panel=')
        );
    }

    public function testUpfrontLedgerIndexIsStyledAsTopOfBookTabs(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/characters/'
            . 'guild-dice.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-open-ledger .gmrc-ledger-index',
            $css
        );
        self::assertStringContainsString(
            'justify-content: flex-start !important;',
            $css
        );
        self::assertStringContainsString(
            'border-bottom:',
            $css
        );
        self::assertStringContainsString(
            'calc(-1 * clamp(1.5rem, 3vw, 2.8rem))',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-ledger-index__label',
            $css
        );
        self::assertStringContainsString(
            'border-radius:',
            $css
        );
    }

    public function testLivingLedgerScriptSupportsKeyboardNavigation(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString("event.key === 'ArrowRight'", $script);
        self::assertStringContainsString("event.key === 'ArrowLeft'", $script);
        self::assertStringContainsString("event.key === 'Home'", $script);
        self::assertStringContainsString("event.key === 'End'", $script);
    }
}
