<?php

namespace GreatMarketrealmCompanion\Core\Routing;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\Response;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
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
        protected Container $container,
        protected Request $request
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
            ?? $this->request->method()
        );
        
        $path = $requestUri !== null
            ? $this->pathFromUri($requestUri)
            : $this->request->path();

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

        $result = $this->runHandler(
            $route['handler'],
            $route['parameters']
        );
        
        if ($result instanceof Response) {
            $result->send();
        }
        
        return $result;
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
    
            $reflection = new ReflectionMethod(
                $controller,
                $controllerMethod
            );
    
            $arguments = $this->resolveArguments(
                $reflection,
                $parameters
            );
    
            return $controller->{$controllerMethod}(
                ...$arguments
            );
        }
    
        $reflection = new ReflectionFunction(
            $handler
        );
    
        $arguments = $this->resolveArguments(
            $reflection,
            $parameters
        );
    
        return $handler(...$arguments);
    }

    /**
     * Resolve arguments for a route handler.
     *
     * @param array<string, string> $routeParameters
     *
     * @return array<int, mixed>
     */
    protected function resolveArguments(
        ReflectionFunctionAbstract $reflection,
        array $routeParameters
    ): array {
        $arguments = [];
    
        foreach ($reflection->getParameters() as $parameter) {
            $arguments[] = $this->resolveParameter(
                $parameter,
                $routeParameters
            );
        }
    
        return $arguments;
    }

    /**
     * Resolve a route handler parameter.
     *
     * @param array<string, string> $routeParameters
     */
    protected function resolveParameter(
        ReflectionParameter $parameter,
        array $routeParameters
    ): mixed {
        $type = $parameter->getType();
    
        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            return $this->resolveClassParameter(
                $type->getName()
            );
        }
    
        $name = $parameter->getName();
    
        if (array_key_exists($name, $routeParameters)) {
            return $this->castRouteParameter(
                $routeParameters[$name],
                $type
            );
        }
    
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
    
        if ($parameter->allowsNull()) {
            return null;
        }
    
        throw new RuntimeException(
            sprintf(
                'Unable to resolve route parameter [%s].',
                $name
            )
        );
    }

    /**
     * Resolve a class-typed handler parameter.
     */
    protected function resolveClassParameter(
        string $className
    ): object {
        if (is_a($className, FormRequest::class, true)) {
            return $this->resolveFormRequest(
                $className
            );
        }
    
        if ($className === Request::class) {
            return $this->request;
        }
    
        if ($this->container->has($className)) {
            return $this->container->make(
                $className
            );
        }
    
        throw new RuntimeException(
            sprintf(
                'Unable to resolve handler dependency [%s].',
                $className
            )
        );
    }

    /**
     * Resolve and validate a form request.
     *
     * @param class-string<FormRequest> $requestClass
     */
    protected function resolveFormRequest(
        string $requestClass
    ): FormRequest {
        $request = new $requestClass();
    
        if (! $request->isAuthorized()) {
            throw new RuntimeException(
                'This request is not authorised.'
            );
        }
    
        $request->validated();
    
        return $request;
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
     * Cast a route parameter to its declared type.
     */
    protected function castRouteParameter(
        string $value,
        ?\ReflectionType $type
    ): mixed {
        if (! $type instanceof ReflectionNamedType) {
            return $value;
        }
    
        return match ($type->getName()) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            ),
            default => $value,
        };
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

    /**
     * Extract and normalise a path from a request URI.
     */
    protected function pathFromUri(
        string $requestUri
    ): string {
        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );
    
        if (! is_string($path)) {
            return '/';
        }
    
        return $this->normalisePath(
            $path
        );
    }

}
