<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Providers;

use PHPUnit\Framework\TestCase;

final class LivingEffectsAssetRegistrationTest extends TestCase
{
    public function testLivingEffectsRunsAfterLivingPortrait(): void
    {
        $root = dirname(__DIR__, 3);
        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            "'gmrc-portrait-studio-living-effects'",
            $provider
        );

        self::assertStringContainsString(
            "['gmrc-portrait-studio-living-portrait']",
            $provider
        );

        self::assertStringContainsString(
            'living-effects.js',
            $provider
        );
    }
}
