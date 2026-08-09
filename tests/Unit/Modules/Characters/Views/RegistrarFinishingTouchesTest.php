<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class RegistrarFinishingTouchesTest extends TestCase
{
    public function testRegisterUsesSharedAdventurerPrompt(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/index.php'
        );

        self::assertIsString($view);

        self::assertSame(
            2,
            substr_count(
                $view,
                'components.entries.register-adventurer-prompt'
            )
        );

        self::assertStringNotContainsString(
            'gmrc-empty-state',
            $view
        );
    }

    public function testFinishingTouchesStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-registrars-finishing-touches',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'registrars-finishing-touches.css'
        );
    }

    public function testSharedPromptContainsGuildCopy(): void
    {
        $root = dirname(__DIR__, 5);

        $component = file_get_contents(
            $root
            . '/app/Views/components/entries/'
            . 'register-adventurer-prompt.php'
        );

        self::assertIsString($component);

        self::assertStringContainsString(
            'The Guild Register Awaits',
            $component
        );

        self::assertStringContainsString(
            'Register Your First Adventurer',
            $component
        );

        self::assertStringContainsString(
            'Register Another Adventurer',
            $component
        );
    }
}
