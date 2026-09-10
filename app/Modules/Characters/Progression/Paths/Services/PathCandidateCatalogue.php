<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

final class PathCandidateCatalogue
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
     * @return array<int,array{
     *     key:string,
     *     label:string,
     *     detail:string
     * }>
     */
    public function forClass(
        CharacterClass $class
    ): array {
        $options = [];

        foreach (
            $this->catalogue->subclasses()
            as $subclass
        ) {
            if (
                ! is_array($subclass)
                || (string) (
                    $subclass['parent']
                    ?? ''
                ) !== $class->value()
            ) {
                continue;
            }

            $key = sanitize_key(
                (string) (
                    $subclass['key']
                    ?? ''
                )
            );

            $label = trim(
                (string) (
                    $subclass['name']
                    ?? ''
                )
            );

            if ($key === '' || $label === '') {
                continue;
            }

            $guide = $this->guides->forPath(
                $key
            );

            $preview = $this->gifts->supports($key)
                ? array_slice(
                    $this->gifts->all($key),
                    0,
                    4
                )
                : $this->expansionGiftPreview($subclass);

            $options[] = [
                'key' => $key,
                'label' => $label,
                'detail' => trim(
                    (string) (
                        $subclass['description']
                        ?? ''
                    )
                ),
                'identity' =>
                    (string) ($guide['identity'] ?? $subclass['identity'] ?? ''),
                'playstyle' =>
                    (string) ($guide['playstyle'] ?? $subclass['playstyle'] ?? ''),
                'best_for' =>
                    (string) ($guide['best_for'] ?? $subclass['best_for'] ?? ''),
                'gift_preview' =>
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
                                (string) (
                                    $gift['label']
                                    ?? ''
                                ),
                            'summary' =>
                                (string) (
                                    $gift['summary']
                                    ?? ''
                                ),
                        ],
                        $preview
                    ),
            ];
        }

        return $options;
    }

    /** @param array<string,mixed> $subclass @return array<int,array<string,mixed>> */
    private function expansionGiftPreview(array $subclass): array
    {
        if (($subclass['source_kind'] ?? '') !== 'gmrexp') {
            return [];
        }

        $features = [];
        foreach ((array) ($subclass['features'] ?? []) as $feature) {
            if (is_array($feature) && is_string($feature['key'] ?? null)) {
                $features[$feature['key']] = $feature;
            }
        }

        $preview = [];
        foreach ((array) ($subclass['progression'] ?? []) as $step) {
            if (! is_array($step)) {
                continue;
            }
            foreach ((array) ($step['features'] ?? []) as $featureKey) {
                $feature = $features[(string) $featureKey] ?? null;
                if (! is_array($feature) || trim((string) ($feature['name'] ?? '')) === '') {
                    continue;
                }
                $preview[] = [
                    'level' => max(0, (int) ($step['level'] ?? 0)),
                    'label' => trim((string) $feature['name']),
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
