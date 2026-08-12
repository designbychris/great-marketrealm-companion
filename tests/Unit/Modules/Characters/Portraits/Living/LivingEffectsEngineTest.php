<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Living;

use PHPUnit\Framework\TestCase;

final class LivingEffectsEngineTest extends TestCase
{
    public function testRaceEffectsAreRegisteredCentrally(): void
    {
        $script = $this->script();

        foreach ([
            "fructan: ['botanical-motes']",
            "fungifolk: ['spore-drift']",
            "'drink-folk': ['bubble-rise']",
            "frostreem: ['frost-motes']",
            "recalled: ['recall-flicker']",
        ] as $contract) {
            self::assertStringContainsString(
                $contract,
                $script
            );
        }
    }

    public function testClassEffectsAreRegisteredCentrally(): void
    {
        $script = $this->script();

        foreach ([
            "artificer: ['artificer-spark']",
            "cleric: ['sacred-glint']",
            "druid: ['nature-motes']",
            "warlock: ['eldritch-wisp']",
            "wizard: ['arcane-glimmer']",
            "'cleaver-saint': ['sacred-glint']",
        ] as $contract) {
            self::assertStringContainsString(
                $contract,
                $script
            );
        }
    }

    public function testEffectsAreDeterministicAndNotRecipeSlots(): void
    {
        $script = $this->script();

        self::assertStringContainsString(
            'const hash = function',
            $script
        );

        self::assertStringNotContainsString(
            'Math.random',
            $script
        );

        self::assertStringContainsString(
            'dataLivingEffectsLayer',
            str_replace(
                '.dataset.livingEffectsLayer',
                'dataLivingEffectsLayer',
                $script
            )
        );
    }

    public function testCustomPortraitsAndReducedMotionDoNotAnimate(): void
    {
        $script = $this->script();
        $styles = $this->styles();

        self::assertStringContainsString(
            "mode === 'custom'",
            $script
        );

        self::assertStringContainsString(
            'reducedMotion.matches',
            $script
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $styles
        );

        self::assertStringContainsString(
            '.gmrc-living-effects',
            $styles
        );
    }

    public function testEngineRespondsToPortraitIdentityChanges(): void
    {
        $script = $this->script();

        self::assertStringContainsString(
            "'data-portrait-race'",
            $script
        );

        self::assertStringContainsString(
            "'data-portrait-class'",
            $script
        );

        self::assertStringContainsString(
            'gmrc:portrait:generation-changed',
            $script
        );
    }

    private function script(): string
    {
        $root = dirname(__DIR__, 6);
        $script = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'living-effects.js'
        );

        self::assertIsString($script);

        return $script;
    }

    private function styles(): string
    {
        $root = dirname(__DIR__, 6);
        $styles = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($styles);

        return $styles;
    }
}
