<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Presentation;

use PHPUnit\Framework\TestCase;

final class FellowshipCompanyPortraitRegressionTest extends TestCase
{
    public function testPortraitComponentHasExplicitCompactAndCompanyVariants(): void
    {
        $root = dirname(__DIR__, 5);
        $component = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'fellowship-portrait.php'
        );

        self::assertIsString($component);
        self::assertStringContainsString(
            "'compact', 'company'",
            $component
        );
        self::assertStringContainsString(
            "data-fellowship-variant=",
            $component
        );
        self::assertStringContainsString(
            "data-fellowship-size=",
            $component
        );
    }

    public function testRegisterUsesCompactPortraitWhileOpenFellowshipUsesCompanyPortrait(): void
    {
        $root = dirname(__DIR__, 5);
        $index = file_get_contents(
            $root . '/app/Modules/Parties/Views/index.php'
        );
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($index);
        self::assertIsString($show);
        self::assertStringContainsString(
            "'variant' => 'compact'",
            $index
        );
        self::assertStringContainsString(
            "'variant' => 'company'",
            $show
        );
        self::assertStringContainsString(
            "'limit' => 5",
            $index
        );
        self::assertStringContainsString(
            "'limit' => 6",
            $show
        );
    }

    public function testCompanyPortraitHasDedicatedTallResponsiveCanvas(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            'Phase III.11.2A — The Company Portrait',
            $css
        );
        self::assertStringContainsString(
            'data-fellowship-variant="company"',
            $css
        );
        self::assertStringContainsString(
            'clamp(',
            $css
        );
        self::assertStringContainsString(
            '43rem',
            $css
        );
        self::assertStringContainsString(
            'aspect-ratio: 16 / 10;',
            $css
        );
    }

    public function testCompanyPortraitHasSpecificLayoutsForOneThroughSixAdventurers(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);

        foreach (range(1, 6) as $size) {
            self::assertStringContainsString(
                'data-fellowship-size="' . $size . '"',
                $css
            );
        }

        self::assertStringContainsString(
            'translateX(-82%)',
            $css
        );
        self::assertStringContainsString(
            'translateX(-106%)',
            $css
        );
        self::assertStringContainsString(
            'translateY(-28%)',
            $css
        );
    }

    public function testCompanyCompositionRetainsTheFrameAndBackgroundExorcism(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-g2-background',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-g2-frame',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-portrait-layer--background',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-portrait-layer--frame',
            $css
        );
        self::assertStringContainsString(
            'display: none !important;',
            $css
        );
    }

    public function testCompanyCanvasHasTabletAndMobileSizingContracts(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '@media (max-width: 1080px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 780px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 520px)',
            $css
        );
        self::assertStringContainsString(
            'min-height: 29rem;',
            $css
        );
    }
}
