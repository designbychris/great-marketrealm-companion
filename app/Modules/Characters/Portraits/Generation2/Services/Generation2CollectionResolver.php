<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;

defined('ABSPATH') || exit;

final class Generation2CollectionResolver
{
    private const SLOT_ORDER = [
        'background',
        'ground_shadow',
        'body_base',
        'body_shadow',
        'body_highlight',
        'heritage',
        'eyes',
        'mouth',
        'outfit_base',
        'outfit_shadow',
        'outfit_highlight',
        'equipment',
        'accessory',
        'class_effects',
        'ambient_effects',
        'guild_ornament',
        'frame',
    ];

    public function __construct(
        private PortraitManifestRepositoryInterface $manifests
    ) {
    }

    public function supports(
        string $race,
        string $characterClass
    ): bool {
        return sanitize_key($race) === 'fructan'
            && sanitize_key($characterClass) === 'grocer'
            && $this->requiredManifests() !== null;
    }

    /**
     * @return array<int,string>
     */
    public function assetIds(
        string $race,
        string $characterClass
    ): array {
        if (! $this->supports($race, $characterClass)) {
            return [];
        }

        $manifests = $this->requiredManifests();

        if ($manifests === null) {
            return [];
        }

        $bySlot = [];

        foreach ($manifests as $manifest) {
            foreach ($manifest->defaults() as $slot => $assetId) {
                $slot = sanitize_key((string) $slot);

                if (is_string($assetId) && $assetId !== '') {
                    $bySlot[$slot][] = $assetId;
                }
            }

            foreach ($manifest->assets() as $asset) {
                $bySlot[$asset->slot()][] = $asset->id();
            }
        }

        $ordered = [];

        foreach (self::SLOT_ORDER as $slot) {
            foreach (
                array_values(
                    array_unique($bySlot[$slot] ?? [])
                ) as $assetId
            ) {
                $ordered[] = $assetId;
            }
        }

        return $ordered;
    }

    /**
     * @return array<int,PortraitManifest>|null
     */
    private function requiredManifests(): ?array
    {
        $ids = [
            'shared-backgrounds',
            'shared-faces',
            'shared-effects',
            'shared-frames',
            'race-fructan',
            'class-grocer',
            'collection-fructan-grocer',
        ];

        $resolved = [];

        foreach ($ids as $id) {
            $manifest = $this->manifests->find($id);

            if (! $manifest instanceof PortraitManifest) {
                return null;
            }

            $resolved[] = $manifest;
        }

        return $resolved;
    }
}
