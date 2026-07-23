<?php

namespace GreatMarketrealmCompanion\Core\Pages;

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Resources\Resource;
use RuntimeException;

defined('ABSPATH') || exit;

abstract class Page
{
    public function __construct(
        protected Resource $resource
    ) {
    }

    abstract public function key(): string;

    abstract public function title(): string;

    abstract public function path(): string;

    /**
     * The route handler for this Page.
     *
     * @return callable|array{class-string, string}
     */
    abstract public function handler(): callable|array;

    public function method(): string
    {
        return 'GET';
    }

    public function resource(): Resource
    {
        return $this->resource;
    }

    /**
     * Register the Page with the Router.
     */
    public function registerRoute(
        Router $router
    ): void {
        $method = strtolower(
            $this->method()
        );

        if (! method_exists($router, $method)) {
            throw new RuntimeException(
                sprintf(
                    'Unsupported HTTP method "%s" for Page "%s".',
                    $this->method(),
                    $this->key()
                )
            );
        }

        $router->{$method}(
            $this->route(),
            $this->handler()
        );
    }
}
