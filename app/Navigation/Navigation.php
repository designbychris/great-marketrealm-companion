<?php

namespace GreatMarketrealmCompanion\Navigation;

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
        $this->add(
            MenuItem::make(
                'dashboard',
                __('Dashboard', 'great-marketrealm-companion'),
                Icons::DASHBOARD,
                'dashboard',
                10
            )
        );
    
        $this->add(
            MenuItem::make(
                'characters',
                __('Characters', 'great-marketrealm-companion'),
                Icons::USERS,
                'characters',
                20
            )
        );
    
        $this->add(
            MenuItem::make(
                'campaigns',
                __('Campaigns', 'great-marketrealm-companion'),
                Icons::MAP,
                'campaigns',
                30
            )
        );
    
        $this->add(
            MenuItem::make(
                'settings',
                __('Settings', 'great-marketrealm-companion'),
                Icons::SETTINGS,
                'settings',
                100
            )
        );
    }
}
