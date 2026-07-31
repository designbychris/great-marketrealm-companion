<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Routing\Router;
use RuntimeException;

/**
 * Test router that records dispatches and route lookups.
 */
final class DispatchingRouterSpy
{
    /**
     * Value returned from dispatch().
     */
    public mixed $dispatchResult = '';

    /**
     * Exception thrown by dispatch().
     */
    public ?RuntimeException $dispatchException = null;

    /**
     * Routes considered available.
     *
     * @var array<int,string>
     */
    private array $availableRoutes = [];

    /**
     * Recorded dispatch calls.
     *
     * @var array<int,array{
     *     method: string|null,
     *     path: string
     * }>
     */
    private array $dispatches = [];

    /**
     * Recorded has() lookups.
     *
     * @var array<int,array{
     *     method: string,
     *     path: string
     * }>
     */
    private array $lookups = [];

    /**
     * The spy does not require Router's production dependencies.
     */
    public function __construct()
    {
        fwrite(STDERR, "Spy ctor\n");
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(
        ?string $method,
        string $path
    ): mixed {
        $this->dispatches[] = [
            'method' => $method,
            'path'   => $path,
        ];

        if ($this->dispatchException !== null) {
            throw $this->dispatchException;
        }

        return $this->dispatchResult;
    }

    /**
     * {@inheritDoc}
     */
    public function has(
        string $method,
        string $path
    ): bool {
        $method = strtoupper($method);

        $this->lookups[] = [
            'method' => $method,
            'path'   => $path,
        ];

        return in_array(
            $method . ':' . $path,
            $this->availableRoutes,
            true
        );
    }

    /**
     * Mark a route as existing.
     */
    public function registerAvailableRoute(
        string $method,
        string $path
    ): void {
        $this->availableRoutes[] =
            strtoupper($method) . ':' . $path;
    }

    /**
     * Return every recorded dispatch.
     *
     * @return array<int,array{
     *     method: string|null,
     *     path: string
     * }>
     */
    public function dispatches(): array
    {
        return $this->dispatches;
    }

    /**
     * Return every route lookup.
     *
     * @return array<int,array{
     *     method: string,
     *     path: string
     * }>
     */
    public function lookups(): array
    {
        return $this->lookups;
    }
}

final class EmptyRouterSpy
{
    public function __construct()
    {
        fwrite(STDERR, "Ctor\n");
    }
}
