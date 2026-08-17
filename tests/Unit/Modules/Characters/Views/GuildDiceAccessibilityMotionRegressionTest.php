<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceAccessibilityMotionRegressionTest extends TestCase
{
    public function testDiceTrayHasExplicitAccessibleRegionAndStatusSemantics(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'role="region"',
            $view
        );
        self::assertStringContainsString(
            'aria-describedby="gmrc-guild-dice-accessibility-note"',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-result-focus',
            $view
        );
        self::assertStringContainsString(
            'tabindex="-1"',
            $view
        );
        self::assertStringContainsString(
            'role="status"',
            $view
        );
        self::assertStringContainsString(
            'Dice results are announced after each roll.',
            $view
        );
    }

    public function testKeyboardRollsMoveFocusToRelevantResultAction(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'let keyboardInteraction = false;',
            $script
        );
        self::assertStringContainsString(
            "tray.addEventListener('keydown'",
            $script
        );
        self::assertStringContainsString(
            "tray.addEventListener('pointerdown'",
            $script
        );
        self::assertStringContainsString(
            'const focusResultAction = function ()',
            $script
        );
        self::assertStringContainsString(
            'criticalAction',
            $script
        );
        self::assertStringContainsString(
            'vitalAction',
            $script
        );
        self::assertStringContainsString(
            'preventScroll: true',
            $script
        );
    }

    public function testReducedMotionSkipsDiceAnimationRuntimeWork(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $css = file_get_contents(
            $root . '/assets/css/modules/characters/guild-dice.css'
        );

        self::assertIsString($script);
        self::assertIsString($css);
        self::assertStringContainsString(
            "const reducedMotion = window.matchMedia(",
            $script
        );
        self::assertStringContainsString(
            "'(prefers-reduced-motion: reduce)'",
            $script
        );
        self::assertStringContainsString(
            'if (!reducedMotion.matches)',
            $script
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
        self::assertStringContainsString(
            'animation: none !important;',
            $css
        );
        self::assertStringContainsString(
            'The joke remains textual when motion is disabled',
            $css
        );
    }

    public function testNaturalReactionsHaveScreenReaderTextWithoutAnimation(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const accessibleAnnouncement = function (',
            $script
        );
        self::assertStringContainsString(
            'Natural 20. Critical hit. A critical damage action is available.',
            $script
        );
        self::assertStringContainsString(
            'Natural 1. Oh dear. Auby says: The Guild has elected not to record that one.',
            $script
        );
        self::assertStringContainsString(
            'Target is reference only.',
            $script
        );
    }

    public function testLargePoolsMobileAndForcedColorsAreHardened(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $css = file_get_contents(
            $root . '/assets/css/modules/characters/guild-dice.css'
        );

        self::assertIsString($script);
        self::assertIsString($css);
        self::assertStringContainsString(
            "stage.classList.toggle('is-huge-pool', values.length > 12);",
            $script
        );
        self::assertStringContainsString(
            '.gmrc-guild-dice-stage.is-huge-pool',
            $css
        );
        self::assertStringContainsString(
            'max-height: calc(100dvh - 1rem);',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            'outline: 3px solid Highlight !important;',
            $css
        );
    }
}
