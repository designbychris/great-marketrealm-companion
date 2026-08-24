<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\AssignCampaignCharacterRequest;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use RuntimeException;

final class ActiveCampaignController
{
    public function __construct(
        private PlayerCampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private CampaignFellowshipRepository $fellowships,
        private CharacterRepository $characters,
        private ViewFactory $views,
        private ResponseFactory $responses,
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

    public function assign(
        string $id,
        AssignCampaignCharacterRequest $request
    ): RedirectResponse {
        $playerId = $this->guardPlayer();
        $campaign = $this->playerCampaign($id, $playerId);
        $this->assertActive($campaign);
        $characterId = CharacterId::fromString($request->characterId());
        $character = $this->characters->findForOwner($characterId, $playerId);

        if (! $character instanceof Character) {
            throw new RuntimeException(
                'That adventurer does not belong to your Guild account.'
            );
        }

        $this->rosters->assignCharacter(
            $campaign,
            $playerId,
            $characterId->value()
        );
        $this->flash->success(
            sprintf('%s is now your nominated adventurer for %s.', $character->name()->value(), $campaign->name())
        );

        return $this->responses->redirect($this->url());
    }

    public function clear(string $id): RedirectResponse
    {
        $playerId = $this->guardPlayer();
        $campaign = $this->playerCampaign($id, $playerId);
        $this->assertActive($campaign);
        $this->rosters->clearCharacterAssignment($campaign, $playerId);
        $this->flash->success('Your Campaign adventurer nomination has been cleared.');

        return $this->responses->redirect($this->url());
    }

    /** @return array<string,mixed> */
    private function present(Campaign $campaign, int $playerId): array
    {
        $owner = get_userdata($campaign->ownerId());
        $assignedIds = [];

        foreach ($this->rosters->members($campaign) as $member) {
            if ($member['player_id'] === $playerId) {
                $assignedIds = $member['character_ids'];
                break;
            }
        }

        $available = $this->characters->allForOwner($playerId);
        $byId = [];

        foreach ($available as $character) {
            $byId[$character->id()->value()] = $character;
        }

        $assigned = [];
        foreach ($assignedIds as $characterId) {
            if (isset($byId[$characterId])) {
                $assigned[] = $byId[$characterId];
            }
        }

        $fellowship = $this->fellowships->linked($campaign);

        return [
            'id' => $campaign->id(),
            'name' => $campaign->name(),
            'description' => $campaign->description(),
            'status' => $campaign->status(),
            'is_archived' => $campaign->isArchived(),
            'dungeon_master' => $owner ? (string) $owner->display_name : 'Dungeon Master',
            'characters' => $available,
            'assigned_characters' => $assigned,
            'fellowship_name' => $fellowship instanceof Party
                ? $fellowship->name()->value()
                : '',
        ];
    }

    private function playerCampaign(string $id, int $playerId): Campaign
    {
        foreach ($this->campaigns->allForPlayer($playerId) as $campaign) {
            if ($campaign->id() === $id) {
                return $campaign;
            }
        }

        throw new RuntimeException('That Campaign is not part of your Active Campaigns.');
    }

    private function assertActive(Campaign $campaign): void
    {
        if ($campaign->isArchived()) {
            throw new RuntimeException('Closed Campaigns preserve their adventurer assignment as history.');
        }
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

    private function url(): string
    {
        return add_query_arg('gmrc_route', 'active-campaigns', home_url('/companion/'));
    }
}
