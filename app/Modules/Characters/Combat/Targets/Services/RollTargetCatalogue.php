<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Services;

use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models\RollTarget;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models\RollTargetKind;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Supplies target categories available to a Character Ledger.
 *
 * Only Self is currently resolved to a concrete Character record. Other
 * categories are descriptive references until a later target registry exists.
 */
final class RollTargetCatalogue
{
    /**
     * @return array<int,array{
     *     kind:string,
     *     label:string,
     *     id:?string,
     *     target_label:string,
     *     resolved:bool
     * }>
     */
    public function forCharacter(Character $character): array
    {
        $self = RollTarget::resolved(
            RollTargetKind::SELF,
            $character->id()->value(),
            $character->name()->value()
        );

        $targets = [
            $self,
            RollTarget::reference(RollTargetKind::ALLY),
            RollTarget::reference(
                RollTargetKind::PLAYER_CHARACTER
            ),
            RollTarget::reference(RollTargetKind::NPC),
            RollTarget::reference(
                RollTargetKind::HOSTILE_CREATURE
            ),
        ];

        return array_map(
            static fn (RollTarget $target): array => [
                'kind' => $target->kind(),
                'label' => RollTargetKind::label(
                    $target->kind()
                ),
                'id' => $target->id(),
                'target_label' => $target->label(),
                'resolved' => $target->isResolved(),
            ],
            $targets
        );
    }
}
