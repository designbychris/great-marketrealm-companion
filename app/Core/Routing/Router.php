<?php

namespace GreatMarketrealmCompanion\Core\Routing;

use GreatMarketrealmCompanion\Core\Application;

defined('ABSPATH') || exit;

/**
 * Router.
 *
 * Registers and dispatches application routes.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class Router
{
    /**
     * Application instance.
     */
    protected Application $app;

    /**
     * Registered routes.
     *
     * @var array<string, array<string, callable|array>>
     */
    protected array $routes = [];

    /**
     * Constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a GET route.
     */
    public function get(
        string $uri,
        callable|array $action
    ): self {
        $this->routes['GET'][$uri] = $action;

        return $this;
    }

    /**
     * Dispatch a request.
     */
    public function dispatch(
    string $httpMethod,
    string $uri
    ): mixed {
    
        $action = $this->routes[$httpMethod][$uri] ?? null;
    
        ...
    
        [$controller, $controllerMethod] = $action;
    
        return $this->app
            ->make($controller)
            ->{$controllerMethod}();
    }

    /**
     * Return registered routes.
     */
    public function routes(): array
    {
        return $this->routes;
    }

    public function has(
    string $httpMethod,
    string $uri
    ): bool
    {
        return isset(
            $this->routes[$httpMethod][$uri]
        );
    }
}
