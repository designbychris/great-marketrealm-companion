<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Pages;

use GreatMarketrealmCompanion\Core\Pages\Page;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Resources\Resource;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PageTest extends TestCase
{
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = $this->createMock(
            Resource::class
        );
    }

    private function makePage(
        string $method = 'GET'
    ): TestPage {
        return new TestPage(
            $this->resource,
            $method
        );
    }

    public function testResourceIsStored(): void
    {
        $page = $this->makePage();

        $this->assertSame(
            $this->resource,
            $page->resource()
        );
    }

    public function testDefaultMethodIsGet(): void
    {
        $page = $this->makePage();

        $this->assertSame(
            'GET',
            $page->method()
        );
    }

    public function testMethodCanBeOverridden(): void
    {
        $page = $this->makePage(
            'POST'
        );

        $this->assertSame(
            'POST',
            $page->method()
        );
    }

    public function testKeyReturnsExpectedValue(): void
    {
        $this->assertSame(
            'dashboard',
            $this->makePage()->key()
        );
    }

    public function testTitleReturnsExpectedValue(): void
    {
        $this->assertSame(
            'Dashboard',
            $this->makePage()->title()
        );
    }

    public function testPathReturnsExpectedValue(): void
    {
        $this->assertSame(
            '/dashboard',
            $this->makePage()->path()
        );
    }

    public function testHandlerReturnsCallable(): void
    {
        $handler = $this->makePage()->handler();

        $this->assertIsCallable(
            $handler
        );
    }

    public function testRegistersGetRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('get')
            ->with(
                '/dashboard',
                $this->isType('callable')
            );

        $this->makePage()->registerRoute(
            $router
        );
    }

    public function testRegistersPostRoute(): void
    {
        $router = $this->createMock(
            Router::class
        );

        $router
            ->expects($this->once())
            ->method('post')
            ->with(
                '/dashboard',
                $this->isType('callable')
            );

        $this->makePage(
            'POST'
        )->registerRoute(
            $router
        );
    }

    public function testUnsupportedMethodThrowsException(): void
    {
        $router = new Router();

        $this->expectException(
            RuntimeException::class
        );

        $this->makePage(
            'TRACE'
        )->registerRoute(
            $router
        );
    }
}
