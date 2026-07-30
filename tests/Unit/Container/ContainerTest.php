<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Container;

use GreatMarketrealmCompanion\Core\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testContainerCanBeCreated(): void
    {
        $container = new Container();

        $this->assertInstanceOf(
            Container::class,
            $container
        );
    }

    public function testContainerResolvesConcreteClasses(): void
    {
        $container = new Container();

        $service = $container->make(TestService::class);

        $this->assertInstanceOf(
            TestService::class,
            $service
        );
    }

    public function testContainerResolvesConstructorDependencies(): void
    {
        $container = new Container();
    
        $service = $container->make(ServiceWithDependency::class);
    
        $this->assertInstanceOf(
            TestDependency::class,
            $service->dependency
        );
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $container = new Container();
    
        $container->singleton(
            TestService::class
        );
    
        $first = $container->make(
            TestService::class
        );
    
        $second = $container->make(
            TestService::class
        );
    
        $this->assertSame(
            $first,
            $second
        );
    }
    
    public function testBindingReturnsNewInstances(): void
    {
        $container = new Container();
    
        $container->bind(
            TestService::class
        );
    
        $first = $container->make(
            TestService::class
        );
    
        $second = $container->make(
            TestService::class
        );
    
        $this->assertNotSame(
            $first,
            $second
        );
    
        $this->assertInstanceOf(
            TestService::class,
            $first
        );
    
        $this->assertInstanceOf(
            TestService::class,
            $second
        );
    }

    public function testInstanceReturnsRegisteredObject(): void
    {
        $container = new Container();
    
        $service = new TestService();
    
        $container->instance(
            TestService::class,
            $service
        );
    
        $resolved = $container->make(
            TestService::class
        );
    
        $this->assertSame(
            $service,
            $resolved
        );
    }
    
}

/**
 * Simple class used for testing auto-wiring.
 */
final class TestService
{
}

final class TestDependency
{
}

final class ServiceWithDependency
{
    public function __construct(
        public TestDependency $dependency
    ) {
    }
}
