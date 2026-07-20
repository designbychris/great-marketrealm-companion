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
        string $method,
        string $uri
    ): mixed {

        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            return null;
        }

        if (is_callable($action)) {
            return $action();
        }

        [$controller, $method] = $action;

        return $this->app
            ->make($controller)
            ->{$method}();
    }

    /**
     * Return registered routes.
     */
    public function routes(): array
    {
        return $this->routes;
    }
}
