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
