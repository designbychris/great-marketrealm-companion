<?php

namespace GreatMarketrealmCompanion\Core\Routing;

use GreatMarketrealmCompanion\Core\Container;
use RuntimeException;

defined('ABSPATH') || exit;

class Router
{
    /**
     * Registered routes.
     *
     * @var array<string, array<string, callable|array>>
     */
    protected array $routes = [];

    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * Register a GET route.
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalisePath($path)] = $handler;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(
        ?string $httpMethod = null,
        ?string $requestUri = null
    ): mixed {
        $httpMethod ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri ??= $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url($requestUri, PHP_URL_PATH);

        if (! is_string($path)) {
            $path = '/';
        }

        $path = $this->normalisePath($path);

        if (! isset($this->routes[$httpMethod][$path])) {
            throw new RuntimeException(
                sprintf('No route registered for [%s %s].', $httpMethod, $path)
            );
        }

        return $this->runHandler(
            $this->routes[$httpMethod][$path]
        );
    }

    /**
     * Execute a route handler.
     */
    protected function runHandler(callable|array $handler): mixed
    {
        if (is_callable($handler)) {
            return $handler();
        }

        [$controllerClass, $controllerMethod] = $handler;

        $controller = $this->container->make($controllerClass);

        return $controller->{$controllerMethod}();
    }

    /**
     * Check whether a route exists.
     */
    public function has(string $httpMethod, string $path): bool
    {
        return isset(
            $this->routes[strtoupper($httpMethod)][$this->normalisePath($path)]
        );
    }

    /**
     * Normalise a route path.
     */
    protected function normalisePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
