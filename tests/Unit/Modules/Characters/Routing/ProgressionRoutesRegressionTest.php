<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Routing;

use PHPUnit\Framework\TestCase;

final class ProgressionRoutesRegressionTest extends TestCase
{
    public function testExperienceProgressionRouteRemainsRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $routes = file_get_contents(
            $root . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);

        self::assertStringContainsString(
            "'/characters/{id}/progression/experience'",
            $routes
        );

        self::assertStringContainsString(
            "[CharacterController::class, 'addExperience']",
            $routes
        );
    }

    public function testExperienceHandlerExistsOnCharacterController(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);

        self::assertStringContainsString(
            'function addExperience(',
            $controller
        );
    }
}
