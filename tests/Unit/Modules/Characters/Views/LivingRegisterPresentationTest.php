<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class LivingRegisterPresentationTest extends TestCase
{
    public function testProgressionSpreadContainsLivingRegister(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-living-register', $view);
        self::assertStringContainsString('The Living Register', $view);
        self::assertStringContainsString('Current certified progression record', $view);
        self::assertStringContainsString("\$livingRegister['path_gift_count']", $view);
        self::assertStringContainsString('Fresh Ink in the Register', $view);
        self::assertStringContainsString("\$livingRegister['fresh_ink']", $view);
        self::assertStringContainsString('data-sealed-chronicle', $view);
        self::assertStringContainsString('The Sealed Chronicle', $view);
        self::assertStringContainsString("\$livingRegister['chronicle']", $view);
        self::assertStringContainsString('data-guild-milestone', $view);
        self::assertStringContainsString("\$livingRegister['milestone_count']", $view);
        self::assertStringContainsString('data-journey-measure', $view);
        self::assertStringContainsString('The Measure of the Journey', $view);
        self::assertStringContainsString("\$livingRegister['journey_measure']", $view);
        self::assertStringContainsString('data-living-change-record', $view);
        self::assertStringContainsString('The Living Record of Change', $view);
        self::assertStringContainsString("\$livingRegister['change_record']", $view);
        self::assertStringContainsString('data-living-register-empty', $view);
        self::assertStringContainsString('data-living-register-final-seal', $view);
        self::assertStringContainsString("\$livingRegister['register_status']", $view);
        self::assertStringNotContainsString('gmrc-rise-certification-history', $view);
        self::assertStringContainsString('Next Guild Certification', $view);
    }

    public function testLivingRegisterRefinementKeepsLongHistoryAccessible(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('aria-describedby="gmrc-living-register-intro"', $view);
        self::assertStringContainsString('data-living-register-index', $view);
        self::assertStringContainsString('aria-label="Living Register sections"', $view);
        self::assertStringContainsString('href="#gmrc-fresh-ink-title"', $view);
        self::assertStringContainsString('href="#gmrc-journey-measure-title"', $view);
        self::assertStringContainsString('href="#gmrc-change-record-title"', $view);
        self::assertStringContainsString('href="#gmrc-sealed-chronicle-title"', $view);
        self::assertStringContainsString('tabindex="-1"', $view);
        self::assertStringContainsString('<span class="screen-reader-text"> to </span>', $view);
        self::assertStringContainsString('Latest certification', $view);
        self::assertStringContainsString("if (! \$progression['is_maximum'])", $view);
    }

    public function testLivingRegisterStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents($root . '/app/Providers/FrontendServiceProvider.php');

        self::assertIsString($provider);
        self::assertStringContainsString('gmrc-living-register', $provider);
        self::assertFileExists(
            $root . '/assets/css/modules/characters/living-register.css'
        );
        $css = file_get_contents($root . '/assets/css/modules/characters/living-register.css');
        self::assertIsString($css);
        self::assertStringContainsString('gmrc-living-register__chronicle', $css);
        self::assertStringContainsString('gmrc-living-register__milestones', $css);
        self::assertStringContainsString('gmrc-living-register__journey-grid', $css);
        self::assertStringContainsString('gmrc-living-register__change-record', $css);
        self::assertStringContainsString('gmrc-living-register__change-moments', $css);
        self::assertStringContainsString('gmrc-living-register__empty-state', $css);
        self::assertStringContainsString('gmrc-living-register__final-seal', $css);
        self::assertStringContainsString('gmrc-living-register__index', $css);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('prefers-reduced-motion:reduce', $css);
    }
}
