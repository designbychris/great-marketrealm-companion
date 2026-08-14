<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Providers;

use PHPUnit\Framework\TestCase;

final class AdvancementChoiceReadinessAssetContractTest extends TestCase
{
    public function testFrontendProviderEnqueuesReadinessController(): void
    {
        $root = dirname(__DIR__, 3);

        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-advancement-choice-readiness',
            $provider
        );

        self::assertStringContainsString(
            'advancement-choice-readiness.js',
            $provider
        );

        self::assertStringContainsString(
            'filemtime(',
            $provider
        );
    }
}
