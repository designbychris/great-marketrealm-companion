<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Presenters;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;

defined('ABSPATH') || exit;

/**
 * Read-only bridge from a Character Ledger to Fellowship membership.
 */
final class CharacterFellowshipPresenter
{
    public function __construct(
        private PartyRepositoryInterface $parties
    ) {
    }

    /**
     * @return array<int,array{
     *     party:Party,
     *     membership:PartyMembership
     * }>
     */
    public function present(
        CharacterId $characterId,
        PartyOwnerId $ownerId
    ): array {
        $results = [];

        foreach ($this->parties->allForOwner($ownerId) as $party) {
            if (! $party instanceof Party) {
                continue;
            }

            $membership = $party->membership($characterId);

            if (! $membership instanceof PartyMembership) {
                continue;
            }

            $results[] = [
                'party' => $party,
                'membership' => $membership,
            ];
        }

        return $results;
    }
}
