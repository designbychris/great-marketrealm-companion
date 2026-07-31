<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Core;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Kernel;
use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

final class KernelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        KernelLifecycle::reset();
    }

    public function testKernelCanBeCreated(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $kernel = new TestKernel(
            $application
        );

        $this->assertInstanceOf(
            Kernel::class,
            $kernel
        );
    }

    public function testKernelRegistersFoundationProviders(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FoundationProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            [
                'register:foundation',
                'boot:foundation',
            ],
            KernelLifecycle::$events
        );
    }

    public function testKernelRegistersApplicationProviders(): void
    {
        $kernel = $this->makeKernel(
            applicationProviders: [
                ApplicationProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            [
                'register:application',
                'boot:application',
            ],
            KernelLifecycle::$events
        );
    }

    public function testKernelRegistersKingdomProviders(): void
    {
        $kernel = $this->makeKernel(
            kingdomProviders: [
                KingdomProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            [
                'register:kingdom',
                'boot:kingdom',
            ],
            KernelLifecycle::$events
        );
    }

    public function testProvidersRegisterInTheExpectedOrder(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FirstFoundationProvider::class,
                SecondFoundationProvider::class,
            ],
            kingdomProviders: [
                KingdomProvider::class,
            ],
            applicationProviders: [
                ApplicationProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            [
                'register:foundation-first',
                'register:foundation-second',
                'register:kingdom',
                'register:application',
                'boot:foundation-first',
                'boot:foundation-second',
                'boot:kingdom',
                'boot:application',
            ],
            KernelLifecycle::$events
        );
    }

    public function testEveryProviderRegistersBeforeAnyProviderBoots(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FirstFoundationProvider::class,
            ],
            kingdomProviders: [
                KingdomProvider::class,
            ],
            applicationProviders: [
                ApplicationProvider::class,
            ]
        );

        $kernel->boot();

        $firstBootPosition = $this->firstEventPositionStartingWith(
            'boot:'
        );

        $lastRegisterPosition = $this->lastEventPositionStartingWith(
            'register:'
        );

        $this->assertGreaterThan(
            $lastRegisterPosition,
            $firstBootPosition
        );
    }

    public function testProvidersBootInRegistrationOrder(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FirstFoundationProvider::class,
                SecondFoundationProvider::class,
            ],
            applicationProviders: [
                ApplicationProvider::class,
            ]
        );

        $kernel->boot();

        $bootEvents = array_values(
            array_filter(
                KernelLifecycle::$events,
                static fn (string $event): bool =>
                    str_starts_with($event, 'boot:')
            )
        );

        $this->assertSame(
            [
                'boot:foundation-first',
                'boot:foundation-second',
                'boot:application',
            ],
            $bootEvents
        );
    }

    public function testDuplicateProviderClassesAreOnlyRegisteredOnce(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FoundationProvider::class,
                FoundationProvider::class,
            ],
            kingdomProviders: [
                FoundationProvider::class,
            ],
            applicationProviders: [
                FoundationProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            [
                'register:foundation',
                'boot:foundation',
            ],
            KernelLifecycle::$events
        );
    }

    public function testRegisterProviderIgnoresAnAlreadyRegisteredProvider(): void
    {
        $kernel = $this->makeKernel();

        $kernel->registerProviderForTest(
            FoundationProvider::class
        );

        $kernel->registerProviderForTest(
            FoundationProvider::class
        );

        $this->assertSame(
            [
                'register:foundation',
            ],
            KernelLifecycle::$events
        );

        $this->assertCount(
            1,
            $kernel->providersForTest()
        );
    }

    public function testRegisterProviderRejectsMissingClasses(): void
    {
        $kernel = $this->makeKernel();

        $this->expectException(
            RuntimeException::class
        );

        $this->expectExceptionMessage(
            'Invalid service provider: MissingKernelTestProvider'
        );

        $kernel->registerProviderForTest(
            'MissingKernelTestProvider'
        );
    }

    public function testRegisterProviderRejectsClassesThatAreNotProviders(): void
    {
        $kernel = $this->makeKernel();

        $this->expectException(
            RuntimeException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Invalid service provider: %s',
                InvalidProvider::class
            )
        );

        $kernel->registerProviderForTest(
            InvalidProvider::class
        );
    }

    public function testProviderIsResolvedThroughTheApplicationContainer(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $provider = new FoundationProvider(
            $application
        );

        $application->instance(
            FoundationProvider::class,
            $provider
        );

        $registry = new TestKingdomRegistry([]);

        $application->instance(
            KingdomRegistry::class,
            $registry
        );

        $kernel = new TestKernel(
            $application,
            [
                FoundationProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertSame(
            $provider,
            $kernel->providersForTest()[0]
        );
    }

    public function testProviderRegisterMethodCanAddServicesToTheApplication(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $registry = new TestKingdomRegistry([]);

        $application->instance(
            KingdomRegistry::class,
            $registry
        );

        $kernel = new TestKernel(
            $application,
            [
                ServiceRegisteringProvider::class,
            ]
        );

        $kernel->boot();

        $this->assertTrue(
            $application->has(
                KernelRegisteredService::class
            )
        );

        $this->assertInstanceOf(
            KernelRegisteredService::class,
            $application->make(
                KernelRegisteredService::class
            )
        );
    }

    public function testRepeatedBootDoesNotRegisterProvidersAgain(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FoundationProvider::class,
            ]
        );

        $kernel->boot();
        $kernel->boot();

        $registerEvents = array_values(
            array_filter(
                KernelLifecycle::$events,
                static fn (string $event): bool =>
                    str_starts_with($event, 'register:')
            )
        );

        $this->assertSame(
            [
                'register:foundation',
            ],
            $registerEvents
        );
    }

    public function testRepeatedBootBootsRegisteredProvidersAgain(): void
    {
        $kernel = $this->makeKernel(
            foundationProviders: [
                FoundationProvider::class,
            ]
        );

        $kernel->boot();
        $kernel->boot();

        $bootEvents = array_values(
            array_filter(
                KernelLifecycle::$events,
                static fn (string $event): bool =>
                    str_starts_with($event, 'boot:')
            )
        );

        $this->assertSame(
            [
                'boot:foundation',
                'boot:foundation',
            ],
            $bootEvents
        );
    }

    public function testApplicationBootDelegatesToItsKernel(): void
    {
        $application = new Application(
            '0.6.0'
        );

        $kernel = new TrackingKernel(
            $application
        );

        $property = new ReflectionProperty(
            Application::class,
            'kernel'
        );

        $property->setAccessible(true);

        $property->setValue(
            $application,
            $kernel
        );

        $application->boot();

        $this->assertSame(
            1,
            $kernel->bootCount
        );
    }

    private function makeKernel(
        array $foundationProviders = [],
        array $kingdomProviders = [],
        array $applicationProviders = []
    ): TestKernel {
        $application = new Application(
            '0.6.0'
        );

        $registry = new TestKingdomRegistry(
            $kingdomProviders
        );

        $application->instance(
            KingdomRegistry::class,
            $registry
        );

        return new TestKernel(
            $application,
            $foundationProviders,
            $applicationProviders
        );
    }

    private function firstEventPositionStartingWith(
        string $prefix
    ): int {
        foreach (KernelLifecycle::$events as $position => $event) {
            if (str_starts_with($event, $prefix)) {
                return $position;
            }
        }

        return -1;
    }

    private function lastEventPositionStartingWith(
        string $prefix
    ): int {
        $position = -1;

        foreach (KernelLifecycle::$events as $index => $event) {
            if (str_starts_with($event, $prefix)) {
                $position = $index;
            }
        }

        return $position;
    }
}

/**
 * Testable Kernel exposing protected lifecycle methods and allowing
 * the real provider collections to be replaced with test fixtures.
 */
final class TestKernel extends Kernel
{
    /**
     * @param array<int, class-string<ServiceProvider>> $foundationProviders
     * @param array<int, class-string<ServiceProvider>> $applicationProviders
     */
    public function __construct(
        Application $app,
        array $foundationProviders = [],
        array $applicationProviders = []
    ) {
        parent::__construct(
            $app
        );

        $this->foundationProviders = $foundationProviders;
        $this->applicationProviders = $applicationProviders;
    }

    public function registerProviderForTest(
        string $providerClass
    ): void {
        $this->registerProvider(
            $providerClass
        );
    }

    /**
     * @return array<int, ServiceProvider>
     */
    public function providersForTest(): array
    {
        return $this->providers;
    }
}

final class TrackingKernel extends Kernel
{
    public int $bootCount = 0;

    public function boot(): void
    {
        $this->bootCount++;
    }
}

final class TestKingdomRegistry extends KingdomRegistry
{
    /**
     * @param array<int, class-string<ServiceProvider>> $providers
     */
    public function __construct(
        private array $testProviders
    ) {
    }

    public function providers(): array
    {
        return $this->testProviders;
    }
}

final class KernelLifecycle
{
    /**
     * @var array<int, string>
     */
    public static array $events = [];

    public static function add(
        string $event
    ): void {
        self::$events[] = $event;
    }

    public static function reset(): void
    {
        self::$events = [];
    }
}

abstract class TrackingServiceProvider extends ServiceProvider
{
    protected string $providerName;

    public function register(): void
    {
        KernelLifecycle::add(
            'register:' . $this->providerName
        );
    }

    public function boot(): void
    {
        KernelLifecycle::add(
            'boot:' . $this->providerName
        );
    }
}

final class FoundationProvider extends TrackingServiceProvider
{
    protected string $providerName = 'foundation';
}

final class FirstFoundationProvider extends TrackingServiceProvider
{
    protected string $providerName = 'foundation-first';
}

final class SecondFoundationProvider extends TrackingServiceProvider
{
    protected string $providerName = 'foundation-second';
}

final class KingdomProvider extends TrackingServiceProvider
{
    protected string $providerName = 'kingdom';
}

final class ApplicationProvider extends TrackingServiceProvider
{
    protected string $providerName = 'application';
}

final class ServiceRegisteringProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            KernelRegisteredService::class
        );
    }
}

final class KernelRegisteredService
{
}

final class InvalidProvider
{
}
