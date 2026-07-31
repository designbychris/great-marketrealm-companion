<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Resources;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Tests\Stubs\TestController;
use GreatMarketrealmCompanion\Tests\Stubs\TestResource;
use GreatMarketrealmCompanion\Tests\Stubs\RouterSpy;
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
        $router = new RouterSpy();

        $this->makeResource()->registerRoutes(
            $router
        );
        
        $this->assertCount(
            7,
            $router->routes()
        );
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

        public function testRegistersAllResourceRoutes(): void
    {
        $router = new RouterSpy();
    
        $this->makeResource()->registerRoutes(
            $router
        );
    
        $this->assertSame(
            [
                [
                    'method' => 'GET',
                    'path' => '/characters',
                    'handler' => [
                        TestController::class,
                        'index',
                    ],
                ],
                [
                    'method' => 'GET',
                    'path' => '/characters/create',
                    'handler' => [
                        TestController::class,
                        'create',
                    ],
                ],
                [
                    'method' => 'POST',
                    'path' => '/characters',
                    'handler' => [
                        TestController::class,
                        'store',
                    ],
                ],
                [
                    'method' => 'GET',
                    'path' => '/characters/{id}/edit',
                    'handler' => [
                        TestController::class,
                        'edit',
                    ],
                ],
                [
                    'method' => 'GET',
                    'path' => '/characters/{id}',
                    'handler' => [
                        TestController::class,
                        'show',
                    ],
                ],
                [
                    'method' => 'PUT',
                    'path' => '/characters/{id}',
                    'handler' => [
                        TestController::class,
                        'update',
                    ],
                ],
                [
                    'method' => 'DELETE',
                    'path' => '/characters/{id}',
                    'handler' => [
                        TestController::class,
                        'destroy',
                    ],
                ],
            ],
            $router->routes()
        );
    }

}
