<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class DiceOfDestinyPresentationTest extends TestCase
{
    public function testCharacterCreatorProvidesDiceOfDestinyControls(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/create.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-dice-of-destiny', $view);
        self::assertStringContainsString('name="ability_method"', $view);
        self::assertStringContainsString('value="rolled"', $view);

        foreach (
            ['strength','dexterity','constitution','intelligence','wisdom','charisma']
            as $ability
        ) {
            self::assertStringContainsString(
                "'" . $ability . "'",
                $view
            );
        }

        self::assertStringContainsString(
            'data-destiny-roll>',
            $view
        );
    }

    public function testRollAllAndAccessibleLiveRegionArePresent(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/create.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-destiny-roll-all', $view);
        self::assertStringContainsString('aria-live="polite"', $view);
    }

    public function testDiceOfDestinyAssetsAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString('gmrc-dice-of-destiny', $provider);
        self::assertFileExists(
            $root . '/assets/js/modules/characters/dice-of-destiny.js'
        );
        self::assertFileExists(
            $root . '/assets/css/modules/characters/dice-of-destiny.css'
        );
    }
}
