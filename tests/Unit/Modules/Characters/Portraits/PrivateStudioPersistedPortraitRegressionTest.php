<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class PrivateStudioPersistedPortraitRegressionTest extends TestCase
{
    public function testLegacyCreatorDoesNotRewritePersistedPortraits(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            "studio.dataset.portraitPersisted === 'true'",
            $script
        );

        self::assertStringContainsString(
            'Persisted portraits are owned by the modular Workbench.',
            $script
        );
    }

    public function testGenerationTwoBuilderLeavesPersistedSvgUntouched(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/generation2.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            "studio.dataset.portraitPersisted === 'true'",
            $script
        );

        self::assertStringContainsString(
            'Private Studio must preserve that markup.',
            $script
        );
    }

    public function testPersistedPortraitPublishesExpandedWardrobeState(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'illuminated-portrait.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-portrait-class-effects=',
            $view
        );

        self::assertStringContainsString(
            'data-portrait-guild-ornament=',
            $view
        );
    }
}
