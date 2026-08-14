<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;

defined('ABSPATH') || exit;

final class PathCandidateCatalogue
{
    public function __construct(
        private ?CharacterCatalogueRepository $catalogue = null
    ) {
        $this->catalogue ??=
            new CharacterCatalogueRepository();
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

            $options[] = [
                'key' => $key,
                'label' => $label,
                'detail' => trim(
                    (string) (
                        $subclass['description']
                        ?? ''
                    )
                ),
            ];
        }

        return $options;
    }
}
