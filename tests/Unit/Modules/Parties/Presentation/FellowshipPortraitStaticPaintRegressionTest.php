<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Presentation;

use PHPUnit\Framework\TestCase;

final class FellowshipPortraitStaticPaintRegressionTest extends TestCase
{
    public function testFellowshipPortraitsForceCompletedStaticPaintState(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            'Phase III.11.1E.2 — Exorcising the Fellowship Portraits',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-portrait',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-member__portrait',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-portrait-layer--race',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-portrait-layer--class',
            $css
        );
        self::assertStringContainsString(
            'opacity: 1 !important;',
            $css
        );
        self::assertStringContainsString(
            'filter: blur(0) saturate(1) !important;',
            $css
        );
        self::assertStringContainsString(
            'animation: none !important;',
            $css
        );
        self::assertStringContainsString(
            'transition: none !important;',
            $css
        );
    }

    public function testCompanyPortraitRemovesGeneratedIndividualBackgroundAndFrameLayers(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-portrait-layer--background',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-portrait-layer--frame',
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
        self::assertStringContainsString(
            'display: none !important;',
            $css
        );
    }

    public function testCompanyPortraitUsesOneSharedBackdropRatherThanMemberFrames(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-fellowship-portrait__canvas {',
            $css
        );
        self::assertStringContainsString(
            'overflow: visible;',
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

    public function testFellowshipComponentsStillUseAuthoritativePortraitViewModels(): void
    {
        $root = dirname(__DIR__, 5);
        $group = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'fellowship-portrait.php'
        );
        $member = file_get_contents(
            $root
            . '/app/Views/components/entries/'
            . 'fellowship-member.php'
        );

        self::assertIsString($group);
        self::assertIsString($member);
        self::assertStringContainsString(
            'PortraitViewModel',
            $group
        );
        self::assertStringContainsString(
            'PortraitViewModel',
            $member
        );
        self::assertStringContainsString(
            '$portrait->svg()',
            $group
        );
        self::assertStringContainsString(
            '$portrait->svg()',
            $member
        );
        self::assertStringNotContainsString(
            'gmrc-illuminated-portrait',
            $group
        );
        self::assertStringNotContainsString(
            'gmrc-illuminated-portrait',
            $member
        );
    }
}
