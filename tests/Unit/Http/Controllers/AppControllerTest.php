<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Http\Controllers;
use GreatMarketrealmCompanion\Tests\Stubs\DispatchingRouterSpy;
use GreatMarketrealmCompanion\Tests\Stubs\ViewFactorySpy;

use PHPUnit\Framework\TestCase;
use GreatMarketrealmCompanion\Http\Controllers\AppController;

final class AppControllerTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        self::assertTrue(true);
    }
    public function testDispatchingRouterSpyCanBeCreated(): void
    {
        $router = new DispatchingRouterSpy();
    
        self::assertInstanceOf(
            DispatchingRouterSpy::class,
            $router
        );
    }
}
