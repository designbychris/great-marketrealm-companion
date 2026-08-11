<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Living;

use PHPUnit\Framework\TestCase;

final class LivingIlluminatorRegressionTest extends TestCase
{
    public function testExpandedPortraitsReceiveRaceLedLivingProfiles(): void
    {
        $script = $this->script();

        foreach (
            [
                "fructan: 'sprightly'",
                "fungifolk: 'sporebound'",
                "'drink-folk': 'effervescent'",
                "boxfolk: 'clockwork'",
                "frostreem: 'frosted'",
                "recalled: 'uncanny'",
            ] as $profile
        ) {
            self::assertStringContainsString($profile, $script);
        }
    }

    public function testGoldenAppleKeepsItsBespokeLivingSystem(): void
    {
        $script = $this->script();

        self::assertStringContainsString(
            "portrait.dataset.portraitGeneration === '2'",
            $script
        );
        self::assertStringContainsString(
            "livingPortrait = 'golden-apple'",
            $script
        );
    }

    public function testLivingProfilesAreAppliedAgainAfterGenerationChanges(): void
    {
        $script = $this->script();

        self::assertStringContainsString(
            "gmrc:portrait:generation-changed",
            $script
        );
        self::assertGreaterThanOrEqual(
            2,
            substr_count($script, 'applyLivingProfile(')
        );
    }

    public function testExpandedMotionRespectsReducedMotion(): void
    {
        $css = $this->styles();

        self::assertStringContainsString(
            'data-living-ready="true"',
            $css
        );
        self::assertStringContainsString(
            'data-living-portrait="effervescent"',
            $css
        );
        self::assertStringContainsString(
            'data-living-portrait="clockwork"',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
    }

    private function script(): string
    {
        $root = dirname(__DIR__, 6);
        $script = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'living-portrait.js'
        );

        self::assertIsString($script);

        return $script;
    }

    private function styles(): string
    {
        $root = dirname(__DIR__, 6);
        $css = file_get_contents(
            $root
            . '/assets/css/components/media/'
            . 'generation2-living-portrait.css'
        );

        self::assertIsString($css);

        return $css;
    }
}
