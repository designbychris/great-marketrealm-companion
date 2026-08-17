<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceworksHardeningSealTest extends TestCase
{
    public function testDiceFamiliesAndAuthoredFormulaBoundsAreSealed(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const SUPPORTED_DICE = [4, 6, 8, 10, 12, 20, 100];',
            $script
        );
        self::assertStringContainsString(
            'const MAX_FREE_DICE = 20;',
            $script
        );
        self::assertStringContainsString(
            '.match(/^(\\d+)d(4|6|8|10|12|20|100)$/i);',
            $script
        );
        self::assertStringContainsString(
            'count < 1',
            $script
        );
        self::assertStringContainsString(
            'count > MAX_FREE_DICE',
            $script
        );
        self::assertStringContainsString(
            'return null;',
            $script
        );
        self::assertStringNotContainsString(
            'Math.min(MAX_FREE_DICE, Math.max(1, Number(match[1])))',
            $script
        );
    }

    public function testD20ModesHaveSafeFallbackAndNaturalSemantics(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            "['normal', 'advantage', 'disadvantage'].includes(mode)",
            $script
        );
        self::assertStringContainsString(
            "? mode\n            : 'normal';",
            $script
        );
        self::assertStringContainsString(
            "safeMode === 'normal'",
            $script
        );
        self::assertStringContainsString(
            "safeMode === 'advantage'",
            $script
        );
        self::assertStringContainsString(
            'Math.max(first, second)',
            $script
        );
        self::assertStringContainsString(
            'Math.min(first, second)',
            $script
        );
        self::assertStringContainsString(
            "return 'natural-20';",
            $script
        );
        self::assertStringContainsString(
            "return 'natural-1';",
            $script
        );
    }

    public function testFreeRollsClampQuantityAndNegativeModifiersSafely(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($script);
        self::assertIsString($view);
        self::assertStringContainsString(
            'const boundedInteger = function (',
            $script
        );
        self::assertStringContainsString(
            'Number.isFinite(numeric)',
            $script
        );
        $freeRollStart = strpos(
            $script,
            'const freeRollDefinition = function ()'
        );
        $freeRollEnd = strpos(
            $script,
            'const addFreeFavourite = function ()',
            $freeRollStart
        );

        self::assertIsInt($freeRollStart);
        self::assertIsInt($freeRollEnd);

        $freeRollBlock = substr(
            $script,
            $freeRollStart,
            $freeRollEnd - $freeRollStart
        );

        self::assertStringContainsString(
            'boundedInteger(',
            $freeRollBlock
        );
        self::assertStringContainsString(
            'MAX_FREE_DICE',
            $freeRollBlock
        );
        self::assertStringContainsString(
            '-99',
            $freeRollBlock
        );
        self::assertStringContainsString(
            '99',
            $freeRollBlock
        );
        self::assertStringContainsString(
            'fallback',
            $script
        );
        self::assertStringContainsString(
            'min="-99"',
            $view
        );
        self::assertStringContainsString(
            'max="99"',
            $view
        );
        self::assertStringContainsString(
            "return numeric >= 0 ? '+' + numeric : String(numeric);",
            $script
        );
    }

    public function testQuickRollsAndHistoryFailSafelyWhenStoredStateIsUnavailable(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'stored.slice(0, MAX_FAVOURITES)',
            $script
        );
        self::assertStringContainsString(
            'stored.slice(0, MAX_HISTORY)',
            $script
        );
        self::assertStringContainsString(
            "window.localStorage.removeItem(favouritesKey);",
            $script
        );
        self::assertStringContainsString(
            "window.sessionStorage.removeItem(historyKey);",
            $script
        );
        self::assertStringContainsString(
            "+ ' — unavailable';",
            $script
        );
        self::assertStringContainsString(
            'roll.disabled = true;',
            $script
        );
        self::assertStringContainsString(
            'findTriggerForFavourite(entry)',
            $script
        );
    }

    public function testScaledArcanaRemainsPhpAuthoritative(): void
    {
        $root = dirname(__DIR__, 5);
        $resolver = file_get_contents(
            $root
            . '/app/Modules/Characters/Arcana/Services/'
            . 'ArcaneRollScalingResolver.php'
        );
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($resolver);
        self::assertIsString($script);
        self::assertStringContainsString(
            "'character-level'",
            $resolver
        );
        self::assertStringContainsString(
            "'slot-level'",
            $resolver
        );
        self::assertStringContainsString(
            "'feature-rank'",
            $resolver
        );
        self::assertStringContainsString(
            'formula: activeTrigger.dataset.rollFormula',
            $script
        );
        self::assertStringNotContainsString(
            'characterLevelScaling',
            $script
        );
        self::assertStringNotContainsString(
            'slotLevelScaling',
            $script
        );
        self::assertStringNotContainsString(
            'featureRankScaling',
            $script
        );
    }

    public function testCriticalsTargetsAndVitalApplicationKeepTheirSafetyBoundaries(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const prepareCriticalFollowUp = function (selection, target)',
            $script
        );
        self::assertStringContainsString(
            'target: target',
            $script
        );
        self::assertStringContainsString(
            'paintTargetResult(critical.target || null);',
            $script
        );
        self::assertStringContainsString(
            'target.resolved === true',
            $script
        );
        self::assertStringContainsString(
            "target.kind === 'self'",
            $script
        );
        self::assertStringContainsString(
            'target.id === characterId',
            $script
        );
        self::assertStringContainsString(
            'vitalForm.requestSubmit();',
            $script
        );
        self::assertStringContainsString(
            'the party or encounter registry can resolve it.',
            $script
        );
    }

    public function testHugePoolsAccessibilityAndReducedMotionAreSealed(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $css = file_get_contents(
            $root . '/assets/css/modules/characters/guild-dice.css'
        );

        self::assertIsString($script);
        self::assertIsString($view);
        self::assertIsString($css);
        self::assertStringContainsString(
            "stage.classList.toggle('is-huge-pool', values.length > 12);",
            $script
        );
        self::assertStringContainsString(
            "const reducedMotion = window.matchMedia(",
            $script
        );
        self::assertStringContainsString(
            'if (!reducedMotion.matches)',
            $script
        );
        self::assertStringContainsString(
            'role="status"',
            $view
        );
        self::assertStringContainsString(
            'data-guild-dice-result-focus',
            $view
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            'Natural 1. Oh dear. Auby says:',
            $script
        );
    }

    public function testDiceworksChapterSealDocumentationExists(): void
    {
        $root = dirname(__DIR__, 5);

        self::assertFileExists(
            $root
            . '/docs/GuildArchives/Development/'
            . 'GuildDiceworksPhase31015.md'
        );
        self::assertFileExists(
            $root
            . '/docs/GuildArchives/Development/'
            . 'GuildDiceworksChapterSeal.md'
        );

        $seal = file_get_contents(
            $root
            . '/docs/GuildArchives/Development/'
            . 'GuildDiceworksChapterSeal.md'
        );

        self::assertIsString($seal);
        self::assertStringContainsString(
            'Phase III.10 — The Guild Diceworks',
            $seal
        );
        self::assertStringContainsString(
            'III.10.15',
            $seal
        );
        self::assertStringContainsString(
            'future encounter',
            strtolower($seal)
        );
    }
}
