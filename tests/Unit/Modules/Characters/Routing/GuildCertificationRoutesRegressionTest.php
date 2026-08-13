<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Routing;

use PHPUnit\Framework\TestCase;

final class GuildCertificationRoutesRegressionTest extends TestCase
{
    public function testCertificationRouteUsesPost(): void
    {
        $root = dirname(__DIR__, 5);

        $routes = file_get_contents(
            $root
            . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);

        self::assertStringContainsString(
            "'/characters/{id}/progression/advance/certify'",
            $routes
        );

        self::assertStringContainsString(
            "[CharacterController::class, 'certifyAdvancement']",
            $routes
        );
    }

    public function testCertificationUsesAdvancementNonceGateway(): void
    {
        $root = dirname(__DIR__, 4);

        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'advance/(?:choice|certify)',
            $provider
        );

        self::assertStringContainsString(
            'gmrc_character_advancement_',
            $provider
        );
    }
}
