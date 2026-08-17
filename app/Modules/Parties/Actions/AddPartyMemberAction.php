<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Exceptions\PartyCharacterUnavailable;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;

defined('ABSPATH') || exit;

final class AddPartyMemberAction extends Action
{
    public function __construct(
        private PartyRepositoryInterface $parties,
        private CharacterRepositoryInterface $characters,
        private PartyFinder $finder
    ) {
    }

    public function handle(
        PartyId $partyId,
        PartyOwnerId $ownerId,
        CharacterId $characterId,
        ?PartyMembershipRole $role = null
    ): Party {
        $party = $this->finder->find(
            $partyId,
            $ownerId
        );

        $character = $this->characters->find(
            $characterId
        );

        if (! $character instanceof Character) {
            throw new PartyCharacterUnavailable(
                'The adventurer is unavailable to this account.'
            );
        }

        $party->addMember(
            $characterId,
            $role
        );

        $this->parties->save($party);

        return $party;
    }
}
