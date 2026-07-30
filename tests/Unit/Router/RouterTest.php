<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Router;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private function makeRouter(
        ?Container $container = null,
        ?Request $request = null
    ): Router {
        return new Router(
            $container ?? new Container(),
            $request ?? new Request()
        );
    }

    public function testRouterCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Router::class,
            $this->makeRouter()
        );
    }

    public function testRouterRegistersGetRoute(): void
    {
        $router = $this->makeRouter();

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
        $router = $this->makeRouter();

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

    public function testRouterDispatchesRouteWithParameters(): void
    {
        $router = $this->makeRouter();

        $router->get(
            '/characters/{id}',
            static fn (string $id): string => $id
        );

        $result = $router->dispatch(
            'GET',
            '/characters/42'
        );

        $this->assertSame(
            '42',
            $result
        );
    }

    public function testRouterCastsIntegerRouteParameter(): void
    {
        $router = $this->makeRouter();

        $router->get(
            '/characters/{id}',
            static fn (int $id): int => $id
        );

        $result = $router->dispatch(
            'GET',
            '/characters/42'
        );

        $this->assertSame(
            42,
            $result
        );
    }

    public function testRouterInjectsRequestIntoHandler(): void
    {
        $request = new Request();

        $router = $this->makeRouter(
            request: $request
        );

        $router->get(
            '/request',
            static fn (Request $request): Request => $request
        );

        $result = $router->dispatch(
            'GET',
            '/request'
        );

        $this->assertSame(
            $request,
            $result
        );
    }

    public function testRouterDispatchesControllerHandler(): void
    {
        $container = new Container();

        $logger = new TestLogger();

        $container->instance(
            TestLogger::class,
            $logger
        );

        $router = $this->makeRouter(
            container: $container
        );

        $router->get(
            '/dashboard',
            [TestController::class, 'index']
        );

        $result = $router->dispatch(
            'GET',
            '/dashboard'
        );

        $this->assertSame(
            $logger,
            $result
        );
    }

    public function testRouterDispatchesRouteWithMultipleParameters(): void
    {
        $router = $this->makeRouter();

        $router->get(
            '/characters/{character}/inventory/{item}',
            static fn (
                string $character,
                string $item
            ): string => $character . ':' . $item
        );

        $result = $router->dispatch(
            'GET',
            '/characters/chris/inventory/sword'
        );

        $this->assertSame(
            'chris:sword',
            $result
        );
    }

    public function testRouterResolvesRouteParametersByName(): void
    {
        $router = $this->makeRouter();

        $router->get(
            '/characters/{character}/inventory/{item}',
            static fn (
                string $item,
                string $character
            ): string => $character . ':' . $item
        );

        $result = $router->dispatch(
            'GET',
            '/characters/chris/inventory/sword'
        );

        $this->assertSame(
            'chris:sword',
            $result
        );
    }

    public function testRouterInjectsContainerDependencyIntoHandler(): void
    {
        $container = new Container();

        $logger = new TestLogger();

        $container->instance(
            TestLogger::class,
            $logger
        );

        $router = $this->makeRouter(
            container: $container
        );

        $router->get(
            '/logger',
            static fn (
                TestLogger $logger
            ): TestLogger => $logger
        );

        $result = $router->dispatch(
            'GET',
            '/logger'
        );

        $this->assertSame(
            $logger,
            $result
        );
    }
}

final class TestController
{
    public function __construct(
        private TestLogger $logger
    ) {
    }

    public function index(): TestLogger
    {
        return $this->logger;
    }
}

final class TestLogger
{
}
