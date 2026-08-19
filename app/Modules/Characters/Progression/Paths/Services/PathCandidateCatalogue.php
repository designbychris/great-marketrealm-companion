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

            $preview = array_slice(
                $this->gifts->all($key),
                0,
                4
            );

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
                    (string) (
                        $guide['identity']
                        ?? ''
                    ),
                'playstyle' =>
                    (string) (
                        $guide['playstyle']
                        ?? ''
                    ),
                'best_for' =>
                    (string) (
                        $guide['best_for']
                        ?? ''
                    ),
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
}
