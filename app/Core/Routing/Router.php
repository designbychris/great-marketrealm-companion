<?php

namespace GreatMarketrealmCompanion\Core\Routing;

use GreatMarketrealmCompanion\Core\Container;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Application router.
 *
 * Registers routes, matches incoming requests,
 * and dispatches route handlers.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class Router
{
    /**
     * Registered routes.
     *
     * @var array<string, array<string, callable|array>>
     */
    protected array $routes = [];

    /**
     * Create the router.
     */
    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * Register a GET route.
     */
    public function get(
        string $path,
        callable|array $handler
    ): void {
        $this->addRoute(
            'GET',
            $path,
            $handler
        );
    }

    /**
     * Register a POST route.
     */
    public function post(
        string $path,
        callable|array $handler
    ): void {
        $this->addRoute(
            'POST',
            $path,
            $handler
        );
    }

    /**
     * Register a PUT route.
     */
    public function put(
        string $path,
        callable|array $handler
    ): void {
        $this->addRoute(
            'PUT',
            $path,
            $handler
        );
    }

    /**
     * Register a PATCH route.
     */
    public function patch(
        string $path,
        callable|array $handler
    ): void {
        $this->addRoute(
            'PATCH',
            $path,
            $handler
        );
    }

    /**
     * Register a DELETE route.
     */
    public function delete(
        string $path,
        callable|array $handler
    ): void {
        $this->addRoute(
            'DELETE',
            $path,
            $handler
        );
    }

    /**
     * Add a route to the collection.
     */
    protected function addRoute(
        string $httpMethod,
        string $path,
        callable|array $handler
    ): void {
        $this->routes[strtoupper($httpMethod)][
            $this->normalisePath($path)
        ] = $handler;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(
        ?string $httpMethod = null,
        ?string $requestUri = null
    ): mixed {
        $httpMethod = strtoupper(
            $httpMethod
            ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET')
        );

        $requestUri ??= $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (! is_string($path)) {
            $path = '/';
        }

        $path = $this->normalisePath($path);

        $route = $this->matchRoute(
            $httpMethod,
            $path
        );

        if ($route === null) {
            throw new RuntimeException(
                sprintf(
                    'No route registered for [%s %s].',
                    $httpMethod,
                    $path
                )
            );
        }

        return $this->runHandler(
            $route['handler'],
            $route['parameters']
        );
    }

    /**
     * Match a request against the registered routes.
     *
     * @return array{
     *     handler: callable|array,
     *     parameters: array<string, string>
     * }|null
     */
    protected function matchRoute(
        string $httpMethod,
        string $path
    ): ?array {
        $routes = $this->routes[$httpMethod] ?? [];

        foreach ($routes as $routePath => $handler) {
            $pattern = $this->routePattern(
                $routePath
            );

            if (! preg_match($pattern, $path, $matches)) {
                continue;
            }

            $parameters = array_filter(
                $matches,
                static fn ($key): bool => is_string($key),
                ARRAY_FILTER_USE_KEY
            );

            return [
                'handler' => $handler,
                'parameters' => $parameters,
            ];
        }

        return null;
    }

    /**
     * Convert a route path into a regular expression.
     */
    protected function routePattern(
        string $routePath
    ): string {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $matches): string {
                return sprintf(
                    '(?P<%s>[^/]+)',
                    $matches[1]
                );
            },
            preg_quote($routePath, '#')
        );

        /*
         * preg_quote also escapes the braces, so restore
         * parameter replacement when needed.
         */
        $pattern = preg_replace_callback(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            static function (array $matches): string {
                return sprintf(
                    '(?P<%s>[^/]+)',
                    $matches[1]
                );
            },
            preg_quote($routePath, '#')
        );

        return '#^' . $pattern . '$#';
    }

    /**
     * Execute a route handler.
     *
     * @param array<string, string> $parameters
     */
    protected function runHandler(
        callable|array $handler,
        array $parameters = []
    ): mixed {
        if (is_array($handler)) {
            [$controllerClass, $controllerMethod] = $handler;

            $controller = $this->container->make(
                $controllerClass
            );

            return $controller->{$controllerMethod}(
                ...array_values($parameters)
            );
        }

        return $handler(
            ...array_values($parameters)
        );
    }

    /**
     * Check whether an exact route is registered.
     */
    public function has(
        string $httpMethod,
        string $path
    ): bool {
        return isset(
            $this->routes[strtoupper($httpMethod)][
                $this->normalisePath($path)
            ]
        );
    }

    /**
     * Normalise a route path.
     */
    protected function normalisePath(
        string $path
    ): string {
        $path = '/' . trim($path, '/');

        return $path === '/'
            ? '/'
            : rtrim($path, '/');
    }
}
