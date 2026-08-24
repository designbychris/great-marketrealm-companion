<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;

defined('ABSPATH') || exit;

final class PlayerCampaignRepository
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignRosterRepository $rosters
    ) {
    }

    /** @return Campaign[] */
    public function allForPlayer(int $playerId): array
    {
        if ($playerId < 1) {
            return [];
        }

        return array_values(array_filter(
            $this->campaigns->all(),
            fn (Campaign $campaign): bool =>
                $this->rosters->hasPlayer($campaign, $playerId)
        ));
    }
}
