<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Menu Item.
 *
 * Represents a single item within the platform navigation.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class MenuItem
{
    /**
     * Constructor.
     *
     * @param string      $key       Unique menu key.
     * @param string      $label     Display label.
     * @param string      $icon      Icon identifier.
     * @param string      $route     Route or page identifier.
     * @param int         $sortOrder Sort order.
     * @param string|null $parent    Parent menu key.
     */
    public function __construct(
        protected string $key,
        protected string $label,
        protected string $icon,
        protected string $route,
        protected int $sortOrder = 100,
        protected ?string $parent = null,
    ) {
    }

    /**
     * Create a new menu item.
     */
    public static function make(
        string $key,
        string $label,
        string $icon,
        string $route,
        int $sortOrder = 100,
        ?string $parent = null,
    ): self {
        return new self(
            $key,
            $label,
            $icon,
            $route,
            $sortOrder,
            $parent
        );
    }

    /**
     * Get the menu key.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Get the label.
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Get the icon.
     */
    public function icon(): string
    {
        return $this->icon;
    }

    /**
     * Get the route.
     */
    public function route(): string
    {
        return $this->route;
    }

    /**
     * Get the sort order.
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * Get the parent key.
     */
    public function parent(): ?string
    {
        return $this->parent;
    }

    /**
     * Determine whether this item has a parent.
     */
    public function hasParent(): bool
    {
        return $this->parent !== null;
    }
}
