<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit;

use GreatMarketrealmCompanion\Tests\Stubs\DispatchingRouterSpy;
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function test_can_create_router_spy(): void
    {
        fwrite(STDERR, "1\n");
        
        $router = new \GreatMarketrealmCompanion\Tests\Stubs\DispatchingRouterSpy();

        fwrite(STDERR, "2\n");

        self::assertInstanceOf(
            EmptyRouterSpy::class,
            $router
        );

        fwrite(STDERR, "3\n");
    }
}
