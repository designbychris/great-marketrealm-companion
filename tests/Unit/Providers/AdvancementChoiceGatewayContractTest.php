<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Providers;

use PHPUnit\Framework\TestCase;

final class AdvancementChoiceGatewayContractTest extends TestCase
{
    public function testGatewayRecognisesAdvancementChoiceNonceAction(): void
    {
        $root = dirname(__DIR__, 3);

        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            '#^characters/([^/]+)/progression/advance/choice$#',
            $provider
        );

        self::assertStringContainsString(
            "return 'gmrc_character_advancement_'",
            $provider
        );
    }

    public function testGatewayAlreadySendsHttpResponsesBeforeExiting(): void
    {
        $root = dirname(__DIR__, 3);

        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'if ($result instanceof Response)',
            $provider
        );

        self::assertStringContainsString(
            '$result->send();',
            $provider
        );
    }
}
