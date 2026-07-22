<?php

namespace GreatMarketrealmCompanion\Resources;

use GreatMarketrealmCompanion\Core\Routing\Router;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Resource Registry.
 *
 * Stores application Resources contributed by Kingdoms.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
class ResourceRegistry
{
    /**
     * Registered Resources.
     *
     * @var array<string, Resource>
     */
    protected array $resources = [];

    /**
     * Register a Resource.
     */
    public function add(
        Resource $resource
    ): void {
        $key = trim(
            $resource->key()
        );

        if ($key === '') {
            throw new InvalidArgumentException(
                'A Resource key cannot be empty.'
            );
        }

        if ($this->has($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Resource [%s] is already registered.',
                    $key
                )
            );
        }

        $this->resources[$key] = $resource;
    }

    /**
     * Determine whether a Resource exists.
     */
    public function has(
        string $key
    ): bool {
        return isset(
            $this->resources[$key]
        );
    }

    /**
     * Retrieve a Resource.
     */
    public function get(
        string $key
    ): ?Resource {
        return $this->resources[$key] ?? null;
    }

    /**
     * Return all Resources.
     *
     * @return array<string, Resource>
     */
    public function all(): array
    {
        return $this->resources;
    }

    /**
     * Register routes for every Resource.
     */
    public function registerRoutes(
        Router $router
    ): void {
        foreach ($this->resources as $resource) {
            $resource->registerRoutes(
                $router
            );
        }
    }

    /**
     * Return the Resource count.
     */
    public function count(): int
    {
        return count(
            $this->resources
        );
    }

    /**
     * Remove all Resources.
     */
    public function clear(): void
    {
        $this->resources = [];
    }
}
