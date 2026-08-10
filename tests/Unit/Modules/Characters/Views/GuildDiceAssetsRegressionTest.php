<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceAssetsRegressionTest extends TestCase
{
    public function testGuildDiceAssetsAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString('gmrc-guild-dice', $provider);
        self::assertFileExists(
            $root . '/assets/css/modules/characters/guild-dice.css'
        );
        self::assertFileExists(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
    }

    public function testDiceEngineSupportsThreeRollModesAndSecureRandomness(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString('getRandomValues', $script);
        self::assertStringContainsString("mode === 'advantage'", $script);
        self::assertStringContainsString("mode === 'disadvantage'", $script);
        self::assertStringContainsString('aria-live', file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        ));
    }
}
