<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Kingdom Registry.
 *
 * Stores and coordinates every registered Companion Kingdom.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class KingdomRegistry
{
    /**
     * Registered Kingdoms.
     *
     * @var array<string, Kingdom>
     */
    protected array $kingdoms = [];

    /**
     * Register a Kingdom.
     *
     * @throws InvalidArgumentException When the Kingdom key is empty
     *                                  or already registered.
     */
    public function add(Kingdom $kingdom): void
    {
        $key = sanitize_key(
            $kingdom->key()
        );

        if ($key === '') {
            throw new InvalidArgumentException(
                'A Kingdom must have a valid key.'
            );
        }

        if ($this->has($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Kingdom "%s" has already been registered.',
                    $key
                )
            );
        }

        $this->kingdoms[$key] = $kingdom;
    }

    /**
     * Determine whether a Kingdom is registered.
     */
    public function has(string $key): bool
    {
        return isset(
            $this->kingdoms[sanitize_key($key)]
        );
    }

    /**
     * Return a registered Kingdom.
     */
    public function get(string $key): ?Kingdom
    {
        $key = sanitize_key($key);

        return $this->kingdoms[$key] ?? null;
    }

    /**
     * Return all registered Kingdoms.
     *
     * @return array<string, Kingdom>
     */
    public function all(): array
    {
        return $this->kingdoms;
    }

    /**
     * Return all Kingdom service providers.
     *
     * @return array<int, class-string>
     */
    public function providers(): array
    {
        $providers = [];
    
        foreach ($this->kingdoms as $kingdom) {
            $provider = $kingdom->provider();
    
            if ($provider !== '') {
                $providers[] = $provider;
            }
        }
    
        return array_values(
            array_unique($providers)
        );
    }

    /**
     * Return every registered Kingdom route file.
     *
     * @return array<int, string>
     */
    public function routeFiles(): array
    {
        $routeFiles = [];

        foreach ($this->kingdoms as $kingdom) {
            foreach ($kingdom->routes() as $routeFile) {
                if (
                    ! is_string($routeFile)
                    || $routeFile === ''
                ) {
                    continue;
                }

                $routeFiles[] = $routeFile;
            }
        }

        return array_values(
            array_unique($routeFiles)
        );
    }

    /**
     * Ask every Kingdom to register its navigation.
     */
    public function registerNavigation(
        Navigation $navigation
    ): void {
        foreach ($this->kingdoms as $kingdom) {
            $kingdom->registerNavigation(
                $navigation
            );
        }
    }

    /**
     * Return the number of registered Kingdoms.
     */
    public function count(): int
    {
        return count($this->kingdoms);
    }

    /**
     * Remove all registered Kingdoms.
     *
     * Primarily useful for testing or rebuilding the registry.
     */
    public function clear(): void
    {
        $this->kingdoms = [];
    }
}
