<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Services\SharedFellowshipAccess;

defined('ABSPATH') || exit;

/**
 * Builds the signed-in Guild member's certified account relationship summary.
 */
final class GuildMembershipSummary
{
    public function __construct(
        private CharacterRepository $characters,
        private CampaignRepository $campaigns,
        private PlayerCampaignRepository $playerCampaigns,
        private SharedFellowshipAccess $fellowships
    ) {
    }

    /** @return array<string,int|string> */
    public function forAccount(int $accountId, string $accountType): array
    {
        $campaigns = $accountType === AccountType::DM
            ? $this->campaigns->allForOwner($accountId)
            : $this->playerCampaigns->allForPlayer($accountId);

        $activeCampaigns = 0;
        $archivedCampaigns = 0;

        foreach ($campaigns as $campaign) {
            if (! $campaign instanceof Campaign) {
                continue;
            }

            if ($campaign->isArchived()) {
                ++$archivedCampaigns;
            } else {
                ++$activeCampaigns;
            }
        }

        $ownedFellowships = 0;
        $sharedFellowships = 0;

        foreach ($this->fellowships->allForAccount($accountId) as $fellowship) {
            if (! $fellowship instanceof Party) {
                continue;
            }

            if ($fellowship->ownerId()->value() === $accountId) {
                ++$ownedFellowships;
            } else {
                ++$sharedFellowships;
            }
        }

        return [
            'characters' => count($this->characters->allForOwner($accountId)),
            'active_campaigns' => $activeCampaigns,
            'archived_campaigns' => $archivedCampaigns,
            'campaign_label' => $accountType === AccountType::DM
                ? 'Campaigns stewarded'
                : 'Campaign memberships',
            'owned_fellowships' => $ownedFellowships,
            'shared_fellowships' => $sharedFellowships,
        ];
    }
}
