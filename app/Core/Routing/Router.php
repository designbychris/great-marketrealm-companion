<?php

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;

defined('ABSPATH') || exit;

return static function (Router $router): void {
    $router->get(
        '/characters',
        [CharacterController::class, 'index']
    );

    $router->get(
        '/characters/create',
        [CharacterController::class, 'create']
    );

    $router->post(
        '/characters',
        [CharacterController::class, 'store']
    );

    $router->get(
        '/characters/{id}',
        [CharacterController::class, 'show']
    );

    $router->get(
        '/characters/{id}/edit',
        [CharacterController::class, 'edit']
    );

    $router->put(
        '/characters/{id}',
        [CharacterController::class, 'update']
    );

    $router->delete(
        '/characters/{id}',
        [CharacterController::class, 'destroy']
    );
};

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
     * Register a GET, POST, PUT, PATCH and DELETE route.
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function patch(string $path, callable|array $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }
    
    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
    
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
            $httpMethod ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET')
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
    
        if (! isset($this->routes[$httpMethod][$path])) {
            throw new RuntimeException(
                sprintf(
                    'No route registered for [%s %s].',
                    $httpMethod,
                    $path
                )
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
