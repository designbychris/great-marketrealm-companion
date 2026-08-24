<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use RuntimeException;

defined('ABSPATH') || exit;

final class ActiveCampaignController
{
    public function __construct(
        private PlayerCampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private ViewFactory $views,
        private FlashStore $flash
    ) {
    }

    public function index(): string
    {
        $playerId = $this->guardPlayer();

        $campaigns = array_map(
            fn (Campaign $campaign): array => $this->present($campaign, $playerId),
            $this->campaigns->allForPlayer($playerId)
        );

        return $this->views->render(View::make(
            'dungeonmaster.active-campaigns.index',
            [
                'campaigns' => $campaigns,
                'flash' => [
                    'success' => $this->flash->get('success'),
                    'error' => $this->flash->get('error'),
                ],
            ]
        ));
    }

    /** @return array<string,mixed> */
    private function present(Campaign $campaign, int $playerId): array
    {
        $owner = get_userdata($campaign->ownerId());
        $characterIds = [];

        foreach ($this->rosters->members($campaign) as $member) {
            if ($member['player_id'] === $playerId) {
                $characterIds = $member['character_ids'];
                break;
            }
        }

        return [
            'id' => $campaign->id(),
            'name' => $campaign->name(),
            'description' => $campaign->description(),
            'status' => $campaign->status(),
            'is_archived' => $campaign->isArchived(),
            'dungeon_master' => $owner ? (string) $owner->display_name : 'Dungeon Master',
            'character_count' => count($characterIds),
        ];
    }

    private function guardPlayer(): int
    {
        $userId = get_current_user_id();

        if ($userId < 1
            || ! user_can($userId, 'gmrc_access_companion')
            || GuildProfile::accountType($userId) !== AccountType::PLAYER) {
            status_header(403);
            throw new RuntimeException(
                'Active Campaigns are available to registered Guild Players.'
            );
        }

        return $userId;
    }
}
