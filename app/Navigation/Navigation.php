<?php

namespace GreatMarketrealmCompanion\Application\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Registry.
 *
 * Stores the application's navigation items.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Navigation
{
    /**
     * Registered navigation items.
     *
     * @var MenuItem[]
     */
    protected array $items = [];

    /**
     * Add a navigation item.
     */
    public function add(MenuItem $item): self
    {
        $this->items[$item->key()] = $item;

        return $this;
    }

    /**
     * Return all navigation items.
     *
     * @return MenuItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Determine if a navigation item exists.
     */
    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get a navigation item.
     */
    public function get(string $key): ?MenuItem
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Remove a navigation item.
     */
    public function remove(string $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Remove all navigation items.
     */
    public function clear(): self
    {
        $this->items = [];

        return $this;
    }

    /**
     * Register the default platform navigation.
     */
    public function registerDefaults(): void
    {
        // Added in Package 3.2.1.6
    }
}
