<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;

defined('ABSPATH') || exit;

final class DeletePartyAction extends Action
{
    public function __construct(
        private PartyRepositoryInterface $parties,
        private PartyFinder $finder
    ) {
    }

    public function handle(
        PartyId $partyId,
        PartyOwnerId $ownerId
    ): void {
        $this->finder->find(
            $partyId,
            $ownerId
        );

        $this->parties->delete(
            $partyId,
            $ownerId
        );
    }
}
