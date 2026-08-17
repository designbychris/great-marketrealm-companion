<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOffice;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;

defined('ABSPATH') || exit;

final class ChangePartyMemberOfficeAction extends Action
{
    public function __construct(
        private PartyRepositoryInterface $parties,
        private PartyFinder $finder
    ) {
    }

    public function handle(
        PartyId $partyId,
        PartyOwnerId $ownerId,
        CharacterId $characterId,
        PartyOffice $office
    ): Party {
        $party = $this->finder->find(
            $partyId,
            $ownerId
        );

        $party->changeMemberOffice(
            $characterId,
            $office
        );

        $this->parties->save($party);

        return $party;
    }
}
