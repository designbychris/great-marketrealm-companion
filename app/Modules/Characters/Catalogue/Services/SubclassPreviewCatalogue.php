<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue\Services;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathChoiceGuideCatalogue;

defined('ABSPATH') || exit;

/**
 * Player-facing creation-time preview data for future subclass intent.
 *
 * Uses the same guide and Path Gift sources as later advancement choices so
 * creation never develops a second, conflicting subclass description system.
 */
final class SubclassPreviewCatalogue
{
    public function __construct(
        private ?CharacterCatalogueRepository $catalogue = null,
        private ?PathChoiceGuideCatalogue $guides = null,
        private ?PathGiftCatalogue $gifts = null
    ) {
        $this->catalogue ??=
            new CharacterCatalogueRepository();

        $this->guides ??=
            new PathChoiceGuideCatalogue();

        $this->gifts ??=
            new PathGiftCatalogue();
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function all(): array
    {
        $previews = [];

        foreach ($this->catalogue->subclasses() as $subclass) {
            if (! is_array($subclass)) {
                continue;
            }

            $key = sanitize_key(
                (string) ($subclass['key'] ?? '')
            );

            $parent = sanitize_key(
                (string) ($subclass['parent'] ?? '')
            );

            $label = trim(
                (string) ($subclass['name'] ?? '')
            );

            if (
                $key === ''
                || $parent === ''
                || $label === ''
            ) {
                continue;
            }

            $guide = $this->guides->forPath($key);

            $giftPreview = $this->gifts->supports($key)
                ? array_slice(
                    $this->gifts->all($key),
                    0,
                    4
                )
                : $this->expansionGiftPreview($subclass);

            $previews[$key] = [
                'key' => $key,
                'parent' => $parent,
                'label' => $label,
                'detail' => trim(
                    (string) (
                        $subclass['description']
                        ?? ''
                    )
                ),
                'identity' =>
                    trim(
                        (string) (
                            $guide['identity']
                            ?? ''
                        )
                    ),
                'playstyle' =>
                    trim(
                        (string) (
                            $guide['playstyle']
                            ?? ''
                        )
                    ),
                'best_for' =>
                    trim(
                        (string) (
                            $guide['best_for']
                            ?? ''
                        )
                    ),
                'gift_preview' =>
                    array_values(
                        array_map(
                            static fn (
                                array $gift
                            ): array => [
                                'level' =>
                                    (int) (
                                        $gift['level']
                                        ?? 0
                                    ),
                                'label' =>
                                    trim(
                                        (string) (
                                            $gift['label']
                                            ?? ''
                                        )
                                    ),
                                'summary' =>
                                    trim(
                                        (string) (
                                            $gift['summary']
                                            ?? ''
                                        )
                                    ),
                            ],
                            $giftPreview
                        )
                    ),
            ];
        }

        return $previews;
    }

    /**
     * Project sourcebook feature previews without registering them as native
     * Companion Path Gifts. Mechanical application remains a later bridge.
     *
     * @param array<string,mixed> $subclass
     * @return array<int,array{level:int,label:string,summary:string}>
     */
    private function expansionGiftPreview(array $subclass): array
    {
        if (($subclass['source_kind'] ?? '') !== 'gmrexp') {
            return [];
        }

        $features = [];
        foreach ((array) ($subclass['features'] ?? []) as $feature) {
            if (! is_array($feature)) {
                continue;
            }
            $featureKey = (string) ($feature['key'] ?? '');
            if ($featureKey !== '') {
                $features[$featureKey] = $feature;
            }
        }

        $preview = [];
        foreach ((array) ($subclass['progression'] ?? []) as $step) {
            if (! is_array($step)) {
                continue;
            }
            $level = max(0, (int) ($step['level'] ?? 0));
            foreach ((array) ($step['features'] ?? []) as $featureKey) {
                $feature = $features[(string) $featureKey] ?? null;
                if (! is_array($feature)) {
                    continue;
                }
                $label = trim((string) ($feature['name'] ?? ''));
                if ($label === '') {
                    continue;
                }
                $preview[] = [
                    'level' => $level,
                    'label' => $label,
                    'summary' => trim((string) ($feature['description'] ?? '')),
                ];
                if (count($preview) >= 4) {
                    return $preview;
                }
            }
        }

        return $preview;
    }

}
