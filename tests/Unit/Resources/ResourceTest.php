<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Resources;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Tests\Stubs\TestController;
use GreatMarketrealmCompanion\Tests\Stubs\TestResource;
use PHPUnit\Framework\TestCase;

final class ResourceTest extends TestCase
{
    private Application $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createMock(
            Application::class
        );
    }

    private function makeResource(): TestResource
    {
        return new TestResource(
            $this->app
        );
    }

    public function testResourceCanBeCreated(): void
    {
        $this->assertInstanceOf(
            TestResource::class,
            $this->makeResource()
        );
    }

    public function testKeyReturnsExpectedValue(): void
    {
        $this->assertSame(
            'characters',
            $this->makeResource()->key()
        );
    }

    public function testSingularNameReturnsExpectedValue(): void
    {
        $this->assertSame(
            'Character',
            $this->makeResource()->singularName()
        );
    }

    public function testPluralNameReturnsExpectedValue(): void
    {
        $this->assertSame(
            'Characters',
            $this->makeResource()->pluralName()
        );
    }

    public function testRoutePrefixReturnsExpectedValue(): void
    {
        $this->assertSame(
            '/characters',
            $this->makeResource()->routePrefix()
        );
    }

    public function testControllerReturnsExpectedClass(): void
    {
        $this->assertSame(
            TestController::class,
            $this->makeResource()->controller()
        );
    }

    public function testPagesReturnsEmptyArrayByDefault(): void
    {
        $this->assertSame(
            [],
            $this->makeResource()->pages()
        );
    }

    public function testRegistersIndexRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('get')
            ->with(
                '/characters',
                [
                    TestController::class,
                    'index',
                ]
            );

        $this->makeResource()->registerRoutes(
            $router
        );
    }

    public function testRegistersCreateRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->exactly(3))
            ->method('get');

        $this->makeResource()->registerRoutes(
            $router
        );
    }

    public function testRegistersStoreRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('post')
            ->with(
                '/characters',
                [
                    TestController::class,
                    'store',
                ]
            );

        $this->makeResource()->registerRoutes(
            $router
        );
    }

    public function testRegistersUpdateRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('put')
            ->with(
                '/characters/{id}',
                [
                    TestController::class,
                    'update',
                ]
            );

        $this->makeResource()->registerRoutes(
            $router
        );
    }

    public function testRegistersDeleteRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('delete')
            ->with(
                '/characters/{id}',
                [
                    TestController::class,
                    'destroy',
                ]
            );

        $this->makeResource()->registerRoutes(
            $router
        );
    }
}
