<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models;

defined('ABSPATH') || exit;

final class PortraitAssetDefinition
{
    /**
     * @param array<int,string> $tags
     * @param array<int,string> $requires
     * @param array<int,string> $excludes
     */
    public function __construct(
        private string $id,
        private string $slot,
        private string $path,
        private string $label,
        private string $variant = '',
        private array $tags = [],
        private array $requires = [],
        private array $excludes = [],
        private bool $animated = false
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function slot(): string
    {
        return $this->slot;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function variant(): string
    {
        return $this->variant;
    }

    /**
     * @return array<int,string>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    /**
     * @return array<int,string>
     */
    public function requires(): array
    {
        return $this->requires;
    }

    /**
     * @return array<int,string>
     */
    public function excludes(): array
    {
        return $this->excludes;
    }

    public function animated(): bool
    {
        return $this->animated;
    }
}
