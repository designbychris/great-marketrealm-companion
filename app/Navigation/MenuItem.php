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
    protected string $id;
    protected string $title = '';
    protected string $icon = '';
    protected string $route = '#';
    protected int $sort = 100;
    protected bool $active = false;
    protected ?string $permission = null;

    /**
     * Create a new menu item.
     */
    public static function make(string $id): self
    {
        $item = new self();
        $item->id = $id;

        return $item;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function route(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function sort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function permission(?string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->active = $active;

        return $this;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function titleText(): string
    {
        return $this->title;
    }

    public function iconName(): string
    {
        return $this->icon;
    }

    public function routeName(): string
    {
        return $this->route;
    }

    public function sortOrder(): int
    {
        return $this->sort;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function permissionName(): ?string
    {
        return $this->permission;
    }
}
