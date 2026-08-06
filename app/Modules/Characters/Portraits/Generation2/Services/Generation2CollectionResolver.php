<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;

defined('ABSPATH') || exit;

/**
 * Resolve complete Generation 2 portrait collections.
 */
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
     * Return ordered asset IDs forming the supported collection.
     *
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

        $slots = [];

        foreach ($manifests as $manifest) {
            foreach ($manifest->defaults() as $slot => $assetId) {
                if (is_string($assetId) && $assetId !== '') {
                    $slots[sanitize_key((string) $slot)] = $assetId;
                }
            }

            foreach ($manifest->assets() as $asset) {
                if (! isset($slots[$asset->slot()])) {
                    $slots[$asset->slot()] = $asset->id();
                }
            }
        }

        $ordered = [];

        foreach (self::SLOT_ORDER as $slot) {
            if (isset($slots[$slot])) {
                $ordered[] = $slots[$slot];
            }
        }

        return array_values(array_unique($ordered));
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
