<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Core;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Kernel;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Resources\ResourceRegistry;
use GreatMarketrealmCompanion\Services\Codex\Codex;
use GreatMarketrealmCompanion\Services\Definitions\Definitions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ApplicationTest extends TestCase
{
    public function testApplicationCanBeCreated(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertInstanceOf(
            Application::class,
            $application
        );
    }

    public function testApplicationReturnsItsVersion(): void
    {
        $application = new Application(
            '0.6.0-alpha1'
        );

        $this->assertSame(
            '0.6.0-alpha1',
            $application->version()
        );
    }

    public function testApplicationCreatesAContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertInstanceOf(
            Container::class,
            $application->container()
        );
    }

    public function testApplicationReturnsTheSameContainerInstance(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertSame(
            $application->container(),
            $application->container()
        );
    }

    public function testApplicationRegistersItselfInTheContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertSame(
            $application,
            $application->make(
                Application::class
            )
        );
    }

    public function testApplicationRegistersItsContainerInTheContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertSame(
            $application->container(),
            $application->make(
                Container::class
            )
        );
    }

    public function testApplicationCreatesAKernel(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertInstanceOf(
            Kernel::class,
            $application->kernel()
        );
    }

    public function testApplicationReturnsTheSameKernelInstance(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertSame(
            $application->kernel(),
            $application->kernel()
        );
    }

    public function testApplicationRegistersItsKernelInTheContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertSame(
            $application->kernel(),
            $application->make(
                Kernel::class
            )
        );
    }

    public function testApplicationCanRegisterAnExistingInstance(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $service = new ApplicationTestService();

        $application->instance(
            ApplicationTestService::class,
            $service
        );

        $this->assertSame(
            $service,
            $application->make(
                ApplicationTestService::class
            )
        );
    }

    public function testApplicationCanRegisterAServiceBinding(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $application->bind(
            ApplicationTestContract::class,
            ApplicationTestService::class
        );

        $service = $application->make(
            ApplicationTestContract::class
        );

        $this->assertInstanceOf(
            ApplicationTestService::class,
            $service
        );
    }

    public function testBoundServicesAreResolvedAsNewInstances(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $application->bind(
            ApplicationTestContract::class,
            ApplicationTestService::class
        );

        $first = $application->make(
            ApplicationTestContract::class
        );

        $second = $application->make(
            ApplicationTestContract::class
        );

        $this->assertNotSame(
            $first,
            $second
        );
    }

    public function testApplicationCanRegisterASingleton(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $application->singleton(
            ApplicationTestContract::class,
            ApplicationTestService::class
        );

        $service = $application->make(
            ApplicationTestContract::class
        );

        $this->assertInstanceOf(
            ApplicationTestService::class,
            $service
        );
    }

    public function testSingletonServicesReturnTheSameInstance(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $application->singleton(
            ApplicationTestContract::class,
            ApplicationTestService::class
        );

        $first = $application->make(
            ApplicationTestContract::class
        );

        $second = $application->make(
            ApplicationTestContract::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }

    public function testApplicationCanDetermineWhetherAServiceExists(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $application->bind(
            ApplicationTestContract::class,
            ApplicationTestService::class
        );

        $this->assertTrue(
            $application->has(
                ApplicationTestContract::class
            )
        );
    }

    public function testApplicationReportsMissingServices(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $this->assertFalse(
            $application->has(
                UnregisteredApplicationTestService::class
            )
        );
    }

    public function testApplicationPassesParametersToTheContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $service = $application->make(
            ParameterisedApplicationTestService::class,
            [
                'name' => 'Auby',
            ]
        );

        $this->assertSame(
            'Auby',
            $service->name
        );
    }

    #[DataProvider('serviceAccessorProvider')]
    public function testApplicationServiceAccessorsResolveFromTheContainer(
        string $method,
        string $serviceClass
    ): void {
        $application = new Application(
            '0.6.0'
        );

        $service = $this->createWithoutConstructor(
            $serviceClass
        );

        $application->instance(
            $serviceClass,
            $service
        );

        $this->assertSame(
            $service,
            $application->{$method}()
        );
    }

    /**
     * @return array<string, array{string, class-string}>
     */
    public static function serviceAccessorProvider(): array
    {
        return [
            'navigation service' => [
                'navigation',
                Navigation::class,
            ],
            'route service' => [
                'route',
                Router::class,
            ],
            'router service' => [
                'router',
                Router::class,
            ],
            'kingdom registry' => [
                'kingdoms',
                KingdomRegistry::class,
            ],
            'resource registry' => [
                'resources',
                ResourceRegistry::class,
            ],
            'request service' => [
                'request',
                Request::class,
            ],
            'definitions service' => [
                'definitions',
                Definitions::class,
            ],
            'response factory' => [
                'response',
                ResponseFactory::class,
            ],
            'codex service' => [
                'codex',
                Codex::class,
            ],
        ];
    }

    /**
     * Create an object without invoking its constructor.
     *
     * This lets the test verify Application's service accessors
     * without pulling each service's dependencies into this unit test.
     *
     * @param class-string $class
     */
    private function createWithoutConstructor(
        string $class
    ): object {
        return (new ReflectionClass($class))
            ->newInstanceWithoutConstructor();
    }
}

interface ApplicationTestContract
{
}

final class ApplicationTestService implements ApplicationTestContract
{
}

final class ParameterisedApplicationTestService
{
    public function __construct(
        public readonly string $name
    ) {
    }
}

final class UnregisteredApplicationTestService
{
}
