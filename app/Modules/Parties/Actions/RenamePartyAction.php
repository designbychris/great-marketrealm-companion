<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;

defined('ABSPATH') || exit;

final class RenamePartyAction extends Action
{
    public function __construct(
        private PartyRepositoryInterface $parties,
        private PartyFinder $finder
    ) {
    }

    public function handle(
        PartyId $partyId,
        PartyOwnerId $ownerId,
        PartyName $name
    ): Party {
        $party = $this->finder->find(
            $partyId,
            $ownerId
        );

        $party->rename($name);

        $this->parties->save($party);

        return $party;
    }
}
