<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Router;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testRouterCanBeCreated(): void
    {
        $router = new Router(
            new Container(),
            new Request()
        );

        $this->assertInstanceOf(
            Router::class,
            $router
        );
    }

    public function testRouterRegistersGetRoute(): void
    {
        $router = new Router(
            new Container(),
            new Request()
        );

        $router->get(
            '/characters',
            static fn (): string => 'characters'
        );

        $this->assertTrue(
            $router->has(
                'GET',
                '/characters'
            )
        );
    }

    public function testRouterDispatchesStaticGetRoute(): void
    {
        $router = new Router(
            new Container(),
            new Request()
        );
    
        $router->get(
            '/characters',
            static fn (): string => 'characters'
        );
    
        $result = $router->dispatch(
            'GET',
            '/characters'
        );
    
        $this->assertSame(
            'characters',
            $result
        );
    }
}
