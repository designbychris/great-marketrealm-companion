<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Parties\Exceptions\PartyNotFound;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;

defined('ABSPATH') || exit;

/**
 * Resolves Fellowships visible to one Guild account.
 *
 * A Fellowship is visible when the account owns it or when one of the
 * account's Characters is a registered member. Ownership remains separate
 * from membership so shared Campaign Fellowships cannot grant steward powers.
 */
final class SharedFellowshipAccess
{
    public function __construct(
        private PartyRepository $parties,
        private CharacterRepository $characters
    ) {
    }

    /** @return Party[] */
    public function allForAccount(int $accountId): array
    {
        $characterIds = $this->characterIds($accountId);
        $visible = [];

        foreach ($this->parties->allAcrossOwners() as $party) {
            if (! $party instanceof Party) {
                continue;
            }

            if (
                $party->ownerId()->value() !== $accountId
                && ! $this->containsAny($party, $characterIds)
            ) {
                continue;
            }

            $visible[$party->id()->value()] = $party;
        }

        return array_values($visible);
    }

    public function findForAccount(PartyId $id, int $accountId): Party
    {
        $party = $this->parties->findAcrossOwners($id);

        if (
            ! $party instanceof Party
            || (
                $party->ownerId()->value() !== $accountId
                && ! $this->containsAny(
                    $party,
                    $this->characterIds($accountId)
                )
            )
        ) {
            throw new PartyNotFound(
                'The requested Fellowship is not available to this Guild account.'
            );
        }

        return $party;
    }

    public function canManage(Party $party, int $accountId): bool
    {
        return $party->ownerId()->value() === $accountId;
    }

    /** @return array<string,true> */
    private function characterIds(int $accountId): array
    {
        $ids = [];

        foreach ($this->characters->allForOwner($accountId) as $character) {
            if (! $character instanceof Character) {
                continue;
            }

            $ids[$character->id()->value()] = true;
        }

        return $ids;
    }

    /** @param array<string,true> $characterIds */
    private function containsAny(Party $party, array $characterIds): bool
    {
        foreach ($party->memberships() as $membership) {
            if (isset($characterIds[$membership->characterId()->value()])) {
                return true;
            }
        }

        return false;
    }
}
