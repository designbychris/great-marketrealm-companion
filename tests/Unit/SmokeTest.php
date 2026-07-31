<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit;

use GreatMarketrealmCompanion\Tests\Stubs\DispatchingRouterSpy;
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function test_can_create_router_spy(): void
    {
        $router = new DispatchingRouterSpy();

        self::assertInstanceOf(
            DispatchingRouterSpy::class,
            $router
        );
    }
}
