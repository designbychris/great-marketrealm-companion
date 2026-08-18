<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;

defined('ABSPATH') || exit;

final class CallingFolio
{
    public function __construct(
        private ?ClassProgressionCatalogue $catalogue = null
    ) {
        $this->catalogue ??=
            new ClassProgressionCatalogue();
    }

    public function build(
        Character $character,
        int $targetLevel
    ): AdvancementFolio {
        $entry = $this->catalogue->forLevel(
            $character->characterClass(),
            $targetLevel
        );

        $automatic = is_array(
            $entry['automatic'] ?? null
        )
            ? $entry['automatic']
            : [];

        $delegated = is_array(
            $entry['delegated'] ?? null
        )
            ? $entry['delegated']
            : [];

        if (
            $automatic !== []
            && $delegated !== []
        ) {
            $summary = sprintf(
                '%s Level %d records %d automatic Calling %s and %d specialist folio %s.',
                $character->characterClass()->label(),
                $targetLevel,
                count($automatic),
                count($automatic) === 1
                    ? 'gain'
                    : 'gains',
                count($delegated),
                count($delegated) === 1
                    ? 'requirement'
                    : 'requirements'
            );
        } elseif ($automatic !== []) {
            $summary = sprintf(
                '%s Level %d records %d automatic Calling %s; no specialist choice is required here.',
                $character->characterClass()->label(),
                $targetLevel,
                count($automatic),
                count($automatic) === 1
                    ? 'gain'
                    : 'gains'
            );
        } elseif ($delegated !== []) {
            $summary = sprintf(
                '%s Level %d has %d specialist folio %s identified by the Calling catalogue.',
                $character->characterClass()->label(),
                $targetLevel,
                count($delegated),
                count($delegated) === 1
                    ? 'requirement'
                    : 'requirements'
            );
        } else {
            $summary = sprintf(
                '%s Level %d is registered in the Calling catalogue; no specialist folio requirements are active in this pass.',
                $character->characterClass()->label(),
                $targetLevel
            );
        }

        return new AdvancementFolio(
            'calling',
            'Calling Folio',
            $summary,
            FolioStatus::READY,
            false,
            [
                'calling' =>
                    $character
                        ->characterClass()
                        ->label(),
                'target_level' => $targetLevel,
                'catalogue_status' =>
                    (string) (
                        $entry['catalogue_status']
                        ?? 'registered'
                    ),
                'automatic_gains' =>
                    count($automatic),
                'delegated_folios' =>
                    count($delegated),
            ],
            [],
            $delegated
        );
    }
}
