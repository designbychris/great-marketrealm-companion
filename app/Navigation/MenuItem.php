<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Class MenuItem
 *
 * Represents a single navigation item.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.2
 */
class MenuItem
{
    /**
     * Unique menu identifier.
     *
     * @var string
     */
    protected string $id;

    /**
     * Menu title.
     *
     * @var string
     */
    protected string $title = '';

    /**
     * Icon name.
     *
     * References an SVG in assets/icons.
     *
     * @var string
     */
    protected string $icon = '';

    /**
     * Route name.
     *
     * @var string
     */
    protected string $route = '#';

    /**
     * Sort order.
     *
     * Lower numbers appear first.
     *
     * @var int
     */
    protected int $sort = 100;

    /**
     * Required permission.
     *
     * Null means visible to everyone.
     *
     * @var string|null
     */
    protected ?string $permission = null;

    /**
     * Optional badge.
     *
     * Examples:
     * 3
     * NEW
     * BETA
     *
     * @var string|null
     */
    protected ?string $badge = null;

    /**
     * Create a new MenuItem.
     */
    public static function make(string $id): self
    {
        $item = new self();

        $item->id = $id;

        return $item;
    }

    /**
     * Set the title.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the icon.
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the route.
     */
    public function route(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Set the sort order.
     */
    public function sort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Set the required permission.
     */
    public function permission(?string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Set the badge.
     */
    public function badge(?string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get the ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the icon.
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the route.
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Get the sort order.
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * Get the permission.
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * Get the badge.
     */
    public function getBadge(): ?string
    {
        return $this->badge;
    }
}
