<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;

defined('ABSPATH') || exit;

final class CreatePartyAction extends Action
{
    public function __construct(
        private PartyRepositoryInterface $parties
    ) {
    }

    public function handle(
        PartyName $name,
        PartyOwnerId $ownerId
    ): Party {
        $party = Party::create(
            PartyId::generate(),
            $name,
            $ownerId
        );

        $this->parties->save($party);

        return $party;
    }
}
