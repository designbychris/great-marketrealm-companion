<?php

namespace GreatMarketrealmCompanion\Core\Pages;

use GreatMarketrealmCompanion\Resources\Resource;
use GreatMarketrealmCompanion\Core\Routing\Router;
use InvalidArgumentException;
use RuntimeException;

defined('ABSPATH') || exit;

class PageRegistry
{
    /**
     * @var array<string, Page>
     */
    protected array $pages = [];

    public function register(
        Resource $resource,
        string $pageClass
    ): void {
        if (! is_subclass_of($pageClass, Page::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s must extend %s.',
                    $pageClass,
                    Page::class
                )
            );
        }

        /** @var Page $page */
        $page = new $pageClass($resource);

        if ($this->has($page->key())) {
            throw new RuntimeException(
                sprintf(
                    'Page "%s" is already registered.',
                    $page->key()
                )
            );
        }

        $this->pages[$page->key()] = $page;
    }

    public function registerResource(
        Resource $resource
    ): void {
        foreach ($resource->pages() as $pageClass) {
            $this->register(
                $resource,
                $pageClass
            );
        }
    }

    /**
     * Register one Page route.
     */
    public function registerRoute(
        string $key,
        Router $router
    ): void {
        $page = $this->get($key);
    
        if ($page === null) {
            throw new RuntimeException(
                sprintf(
                    'Page "%s" is not registered.',
                    $key
                )
            );
        }
    
        $page->registerRoute($router);
    }

    /**
     * Register every discovered Page.
     */
    public function registerRoutes(
        Router $router
    ): void {
        foreach ($this->pages as $page) {
            $page->registerRoute($router);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->pages[$key]);
    }

    public function get(string $key): ?Page
    {
        return $this->pages[$key] ?? null;
    }

    /**
     * @return array<string, Page>
     */
    public function all(): array
    {
        return $this->pages;
    }

    public function registerRoutes(
    Router $router
    ): void {
        foreach ($this->pages as $page) {
            $page->registerRoute($router);
        }
    }
}
