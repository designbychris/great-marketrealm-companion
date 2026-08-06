<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models;

defined('ABSPATH') || exit;

final class PortraitManifest
{
    /**
     * @param array<string,string> $defaults
     * @param array<int,string> $compatibleWith
     * @param array<int,PortraitAssetDefinition> $assets
     */
    public function __construct(
        private string $id,
        private string $type,
        private string $label,
        private string $directory,
        private array $defaults,
        private array $compatibleWith,
        private array $assets
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function directory(): string
    {
        return $this->directory;
    }

    /**
     * @return array<string,string>
     */
    public function defaults(): array
    {
        return $this->defaults;
    }

    /**
     * @return array<int,string>
     */
    public function compatibleWith(): array
    {
        return $this->compatibleWith;
    }

    /**
     * @return array<int,PortraitAssetDefinition>
     */
    public function assets(): array
    {
        return $this->assets;
    }
}
