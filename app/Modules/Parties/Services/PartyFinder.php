<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Services;

use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Exceptions\PartyNotFound;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;

defined('ABSPATH') || exit;

final class PartyFinder
{
    public function __construct(
        private PartyRepositoryInterface $parties
    ) {
    }

    public function find(
        PartyId $partyId,
        PartyOwnerId $ownerId
    ): Party {
        $party = $this->parties->findForOwner(
            $partyId,
            $ownerId
        );

        if (! $party instanceof Party) {
            throw new PartyNotFound(
                'The requested Fellowship could not be found for this owner.'
            );
        }

        return $party;
    }

    /**
     * @return Party[]
     */
    public function all(
        PartyOwnerId $ownerId
    ): array {
        return $this->parties->allForOwner(
            $ownerId
        );
    }
}
