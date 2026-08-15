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

    public function testDiceworksSupportsNaturalD20ReactionsAndLonelyConfetti(): void
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
        self::assertStringContainsString("return 'natural-20';", $script);
        self::assertStringContainsString("return 'natural-1';", $script);
        self::assertStringContainsString('for (let index = 0; index < 28; index += 1)', $script);
        self::assertStringContainsString('addConfettiPiece(0, true)', $script);
        self::assertStringContainsString('One. Lonely. Piece. Of. Confetti.', $script);
        self::assertStringContainsString('The Guild has elected not to record that one.', $script);
        self::assertStringContainsString('data-guild-dice-reaction', $view);
        self::assertStringContainsString('data-guild-dice-confetti', $view);
        self::assertStringContainsString('gmrc-guild-confetti-burst', $css);
        self::assertStringContainsString('gmrc-guild-confetti-lonely', $css);
        self::assertStringContainsString('prefers-reduced-motion: reduce', $css);
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
