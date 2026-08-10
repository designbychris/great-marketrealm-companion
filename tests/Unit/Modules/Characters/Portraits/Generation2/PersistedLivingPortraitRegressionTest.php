<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class PersistedLivingPortraitRegressionTest extends TestCase
{
    public function testServerRendererEmitsLiveGenerationTwoClasses(): void
    {
        $root = dirname(__DIR__, 6);

        $renderer = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Generation2/'
            . 'Rendering/Generation2PortraitRenderer.php'
        );

        self::assertIsString($renderer);

        self::assertStringContainsString('gmrc-g2-breathing-group', $renderer);
        self::assertStringContainsString('gmrc-g2-eyes', $renderer);
        self::assertStringContainsString('gmrc-g2-eyelids', $renderer);
        self::assertStringContainsString('g2-auby-finishing-touch-01', $renderer);
        self::assertStringContainsString('continue;', $renderer);
    }

    public function testPersistedPortraitIsMarkedReadyByLivingController(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/portrait-studio/'
            . 'living-portrait.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString('portraitPersisted', $script);
        self::assertStringContainsString("illuminationReady = 'true'", $script);
    }

    public function testPersistedPortraitUsesStaticApprovalSeal(): void
    {
        $root = dirname(__DIR__, 6);

        $view = file_get_contents(
            $root
            . '/app/Views/components/media/illuminated-portrait.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-portrait-persisted', $view);
        self::assertStringContainsString("? 'static'", $view);
    }
}
