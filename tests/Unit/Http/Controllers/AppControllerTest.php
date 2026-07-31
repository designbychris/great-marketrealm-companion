<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Http\Controllers;

use GreatMarketrealmCompanion\Core\Http\Response;
use GreatMarketrealmCompanion\Http\Controllers\AppController;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Tests\Stubs\DispatchingRouterSpy;
use GreatMarketrealmCompanion\Tests\Stubs\LayoutRendererSpy;
use GreatMarketrealmCompanion\Tests\Stubs\RouteResolverStub;
use GreatMarketrealmCompanion\Tests\Stubs\ViewFactorySpy;
use PHPUnit\Framework\TestCase;

final class AppControllerTest extends TestCase
{
    public function testDispatchesResolvedRoute(): void
    {
        $router = new DispatchingRouterSpy();

        self::assertSame([], $router->dispatches());

    }

    public function testReturnsResponseObjectsUnchanged(): void
    {
        $router = new DispatchingRouterSpy();

        $response = new Response(
            'Hello World'
        );

        $router->dispatchResult = $response;

        $controller = new AppController(
            $router,
            new ViewFactorySpy(),
            new Navigation(),
            new RouteResolverStub(),
            new LayoutRendererSpy()
        );

        self::assertSame(
            $response,
            $controller->handle()
        );
    }

    public function testRendersStringResponsesInLayout(): void
    {
        $router = new DispatchingRouterSpy();

        $router->dispatchResult = '<h1>Hello</h1>';

        $layout = new LayoutRendererSpy();

        $controller = new AppController(
            $router,
            new ViewFactorySpy(),
            new Navigation(),
            new RouteResolverStub(),
            $layout
        );

        $result = $controller->handle();

        self::assertSame(
            '<layout><h1>Hello</h1></layout>',
            $result
        );

        self::assertSame(
            '<h1>Hello</h1>',
            $layout->lastData()['content']
        );

        self::assertSame(
            'dashboard',
            $layout->lastData()['currentRoute']
        );
    }
}
