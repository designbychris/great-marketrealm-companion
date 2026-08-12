<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Routing;

use PHPUnit\Framework\TestCase;

final class AdvancementRoutesRegressionTest extends TestCase
{
    public function testAdvancementLedgerRouteIsRegistered(): void
    {
        $root = dirname(__DIR__, 5);

        $routes = file_get_contents(
            $root . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/characters/{id}/progression/advance'",
            $routes
        );
        self::assertStringContainsString(
            "[CharacterController::class, 'advancement']",
            $routes
        );
    }

    public function testControllerProvidesAdvancementPreview(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);
        self::assertStringContainsString(
            'function advancement(',
            $controller
        );
        self::assertStringContainsString(
            'new AdvancementLedgerPresenter()',
            $controller
        );
    }
}
