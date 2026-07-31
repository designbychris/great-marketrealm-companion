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
        fwrite(STDERR, "1\n");
    
        $router = new DispatchingRouterSpy();
    
        fwrite(STDERR, "2\n");
    
        self::assertInstanceOf(
            DispatchingRouterSpy::class,
            $router
        );
    
        fwrite(STDERR, "3\n");
    }
}
