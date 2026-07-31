<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Routing\Router;

/**
 * Test router that records registered routes.
 */
final class RouterSpy extends Router
{
    /**
     * Recorded routes.
     *
     * @var array<int, array{
     *     method: string,
     *     path: string,
     *     handler: callable|array
     * }>
     */
    private array $recordedRoutes = [];

    /**
     * The spy does not require Router's production dependencies because
     * it only records route registrations and never dispatches requests.
     */
    public function __construct()
    {
    }

    public function get(
        string $path,
        callable|array $handler
    ): void {
        $this->record(
            'GET',
            $path,
            $handler
        );
    }

    public function post(
        string $path,
        callable|array $handler
    ): void {
        $this->record(
            'POST',
            $path,
            $handler
        );
    }

    public function put(
        string $path,
        callable|array $handler
    ): void {
        $this->record(
            'PUT',
            $path,
            $handler
        );
    }

    public function patch(
        string $path,
        callable|array $handler
    ): void {
        $this->record(
            'PATCH',
            $path,
            $handler
        );
    }

    public function delete(
        string $path,
        callable|array $handler
    ): void {
        $this->record(
            'DELETE',
            $path,
            $handler
        );
    }

    /**
     * Return every recorded route.
     *
     * @return array<int, array{
     *     method: string,
     *     path: string,
     *     handler: callable|array
     * }>
     */
    public function routes(): array
    {
        return $this->recordedRoutes;
    }

    /**
     * Determine whether a particular route was recorded.
     */
    public function hasRecorded(
        string $method,
        string $path
    ): bool {
        $method = strtoupper($method);

        foreach ($this->recordedRoutes as $route) {
            if (
                $route['method'] === $method
                && $route['path'] === $path
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Record a route registration.
     */
    private function record(
        string $method,
        string $path,
        callable|array $handler
    ): void {
        $this->recordedRoutes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }
}
