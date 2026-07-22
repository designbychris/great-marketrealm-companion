<?php

namespace GreatMarketrealmCompanion\Resources;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Routing\Router;

defined('ABSPATH') || exit;

/**
 * Base application Resource.
 *
 * A Resource represents a manageable application entity,
 * such as characters, campaigns, or inventory items.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
abstract class Resource
{
    /**
     * Create the Resource.
     */
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * Unique Resource key.
     */
    abstract public function key(): string;

    /**
     * Singular display name.
     */
    abstract public function singularName(): string;

    /**
     * Plural display name.
     */
    abstract public function pluralName(): string;

    /**
     * Base route path.
     *
     * Example: /characters
     */
    abstract public function routePrefix(): string;

    /**
     * Controller class used by the Resource.
     *
     * @return class-string
     */
    abstract public function controller(): string;

    /**
     * Register the Resource routes.
     */
    public function registerRoutes(
        Router $router
    ): void {
        $prefix = $this->normaliseRoutePrefix(
            $this->routePrefix()
        );

        $controller = $this->controller();

        /*
         * Static routes must be registered before parameterised
         * routes so /create is not interpreted as an ID.
         */
        $router->get(
            $prefix,
            [$controller, 'index']
        );

        $router->get(
            $prefix . '/create',
            [$controller, 'create']
        );

        $router->post(
            $prefix,
            [$controller, 'store']
        );

        $router->get(
            $prefix . '/{id}/edit',
            [$controller, 'edit']
        );

        $router->get(
            $prefix . '/{id}',
            [$controller, 'show']
        );

        $router->put(
            $prefix . '/{id}',
            [$controller, 'update']
        );

        $router->delete(
            $prefix . '/{id}',
            [$controller, 'destroy']
        );
    }

    /**
     * Access the application instance.
     */
    protected function app(): Application
    {
        return $this->app;
    }

    /**
     * Normalise the Resource route prefix.
     */
    protected function normaliseRoutePrefix(
        string $prefix
    ): string {
        $prefix = '/' . trim(
            $prefix,
            '/'
        );

        return $prefix === '/'
            ? '/'
            : rtrim($prefix, '/');
    }
}
