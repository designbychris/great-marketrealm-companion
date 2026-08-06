<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;

defined('ABSPATH') || exit;

final class PortraitAssetCatalogue
{
    public function __construct(
        private PortraitManifestRepositoryInterface $manifests
    ) {
    }

    /**
     * @return array<int,PortraitAssetDefinition>
     */
    public function all(): array
    {
        $assets = [];

        foreach ($this->manifests->all() as $manifest) {
            array_push($assets, ...$manifest->assets());
        }

        return $assets;
    }

    public function find(string $assetId): ?PortraitAssetDefinition
    {
        foreach ($this->all() as $asset) {
            if ($asset->id() === $assetId) {
                return $asset;
            }
        }

        return null;
    }

    /**
     * @return array<int,PortraitAssetDefinition>
     */
    public function forSlot(string $slot): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (PortraitAssetDefinition $asset): bool =>
                    $asset->slot() === $slot
            )
        );
    }
}
