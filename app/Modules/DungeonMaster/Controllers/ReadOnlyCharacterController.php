<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class ReadOnlyCharacterController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private CharacterRepository $characters,
        private DungeonMasterAccess $access,
        private CharacterController $characterLedger
    ) {
    }

    public function show(string $id, string $characterId): string
    {
        $this->guard();
        $campaign = $this->campaigns->findForOwner(
            $id,
            get_current_user_id()
        );

        if (! $campaign instanceof Campaign) {
            throw new RuntimeException(
                'Campaign not found in this Dungeon Master’s Register.'
            );
        }

        if ($campaign->isArchived()) {
            throw new RuntimeException(
                'Archived Campaign records do not grant live Character Ledger access.'
            );
        }

        $ownerId = $this->rosteredOwnerId($campaign, $characterId);

        if ($ownerId === null) {
            status_header(403);
            throw new RuntimeException(
                'That adventurer is not currently assigned to this Campaign Roster.'
            );
        }

        $character = $this->characters->findForOwner(
            CharacterId::fromString($characterId),
            $ownerId
        );

        if ($character === null) {
            throw new RuntimeException(
                'The assigned adventurer could not be found in the Guild Register.'
            );
        }

        return $this->characterLedger->renderReadOnlyForCampaign(
            $character,
            $campaign->id(),
            $campaign->name()
        );
    }

    private function guard(): void
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException(
                'This Character Ledger projection is sealed to Dungeon Masters.'
            );
        }
    }

    private function rosteredOwnerId(
        Campaign $campaign,
        string $characterId
    ): ?int {
        foreach ($this->rosters->members($campaign) as $membership) {
            if (in_array($characterId, $membership['character_ids'], true)) {
                return (int) $membership['player_id'];
            }
        }

        return null;
    }
}
