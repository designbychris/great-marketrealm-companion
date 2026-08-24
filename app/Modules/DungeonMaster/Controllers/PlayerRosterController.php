<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MarketPassRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\AddRosterPlayerRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\LinkCampaignFellowshipRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignFellowshipService;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignMembershipSynchronizer;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use RuntimeException;
use WP_User;

defined('ABSPATH') || exit;

final class PlayerRosterController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private MarketPassRepository $passes,
        private CampaignFellowshipRepository $campaignFellowships,
        private CampaignFellowshipService $campaignFellowshipService,
        private CampaignMembershipSynchronizer $membershipSync,
        private PartyRepository $parties,
        private CharacterRepository $characters,
        private DungeonMasterAccess $access,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function index(string $id): string
    {
        $campaign = $this->campaign($id);

        return $this->views->render(
            View::make(
                'dungeonmaster.players.index',
                [
                    'campaign' => $campaign,
                    'members' => $this->memberData($campaign),
                    'marketPass' => $this->passes->current($campaign),
                    'campaignFellowship' => $this->campaignFellowships->linked($campaign),
                    'availableFellowships' => $this->parties->allForOwner(
                        PartyOwnerId::fromInt($campaign->ownerId())
                    ),
                    'flash' => [
                        'success' => $this->flash->get('success'),
                        'error' => $this->flash->get('error'),
                    ],
                ]
            )
        );
    }

    public function store(
        string $id,
        AddRosterPlayerRequest $request
    ): RedirectResponse {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $player = $this->findPlayer($request->identity());

        if (! $player instanceof WP_User) {
            $this->flash->error(
                'No eligible Player Guild account matched that exact username or email.'
            );

            return $this->responses->redirect($this->url($id));
        }

        $this->rosters->addPlayer($campaign, (int) $player->ID);
        $this->membershipSync->synchronize($campaign);
        $this->flash->success(
            sprintf('%s has joined the Campaign Roster.', $player->display_name)
        );

        return $this->responses->redirect($this->url($id));
    }

    public function foundFellowship(string $id): RedirectResponse
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $party = $this->campaignFellowshipService->found($campaign);
        $this->flash->success(
            sprintf('%s has been founded from the nominated adventuring company.', $party->name()->value())
        );

        return $this->responses->redirect($this->url($id));
    }

    public function linkFellowship(
        string $id,
        LinkCampaignFellowshipRequest $request
    ): RedirectResponse {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $party = $this->campaignFellowshipService->linkExisting(
            $campaign,
            $request->partyId()
        );
        $this->flash->success(
            sprintf('%s is now the Campaign Fellowship.', $party->name()->value())
        );

        return $this->responses->redirect($this->url($id));
    }

    public function unlinkFellowship(string $id): RedirectResponse
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $this->campaignFellowshipService->unlink($campaign);
        $this->flash->success(
            'The Campaign Fellowship link has been released. The Fellowship itself remains safely in the Guild Register.'
        );

        return $this->responses->redirect($this->url($id));
    }

    public function destroy(string $id, int $playerId): RedirectResponse
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $this->rosters->removePlayer($campaign, $playerId);
        $this->membershipSync->synchronize($campaign);
        $this->flash->success('The Player has been removed from this Campaign Roster.');

        return $this->responses->redirect($this->url($id));
    }

    public function attachCharacter(
        string $id,
        int $playerId,
        string $characterId
    ): RedirectResponse {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $this->assertRosteredPlayer($campaign, $playerId);
        $character = $this->characters->findForOwner(
            CharacterId::fromString($characterId),
            $playerId
        );

        if ($character === null) {
            throw new RuntimeException(
                'That Character does not belong to the selected Campaign Player.'
            );
        }

        $this->rosters->attachCharacter($campaign, $playerId, $characterId);
        $this->membershipSync->synchronize($campaign);
        $this->flash->success('The Character has been attached to this campaign.');

        return $this->responses->redirect($this->url($id));
    }

    public function detachCharacter(
        string $id,
        int $playerId,
        string $characterId
    ): RedirectResponse {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $this->assertRosteredPlayer($campaign, $playerId);
        $this->rosters->detachCharacter($campaign, $playerId, $characterId);
        $this->membershipSync->synchronize($campaign);
        $this->flash->success('The Character has been detached from this campaign.');

        return $this->responses->redirect($this->url($id));
    }

    private function campaign(string $id): Campaign
    {
        $this->guard();
        $campaign = $this->campaigns->findForOwner($id, get_current_user_id());

        if (! $campaign instanceof Campaign) {
            throw new RuntimeException(
                'Campaign not found in this Dungeon Master’s Register.'
            );
        }

        return $campaign;
    }

    private function guard(): void
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException(
                'This Campaign Roster is sealed to Dungeon Masters.'
            );
        }
    }

    private function findPlayer(string $identity): ?WP_User
    {
        $user = get_user_by('login', $identity);

        if (! $user instanceof WP_User && is_email($identity)) {
            $user = get_user_by('email', $identity);
        }

        if (! $user instanceof WP_User) {
            return null;
        }

        if (! user_can($user->ID, 'gmrc_access_companion')) {
            return null;
        }

        return GuildProfile::accountType((int) $user->ID) === AccountType::PLAYER
            ? $user
            : null;
    }

    private function assertActive(Campaign $campaign): void
    {
        if ($campaign->isArchived()) {
            throw new RuntimeException(
                'Archived campaigns have a read-only Player Roster.'
            );
        }
    }

    private function assertRosteredPlayer(Campaign $campaign, int $playerId): void
    {
        if (! $this->rosters->hasPlayer($campaign, $playerId)) {
            throw new RuntimeException(
                'That Player is not part of this Campaign Roster.'
            );
        }
    }

    /** @return array<int,array<string,mixed>> */
    private function memberData(Campaign $campaign): array
    {
        $members = [];

        foreach ($this->rosters->members($campaign) as $membership) {
            $playerId = $membership['player_id'];
            $user = get_userdata($playerId);

            if (! $user instanceof WP_User) {
                continue;
            }

            $members[] = [
                'user' => $user,
                'portrait_id' => GuildProfile::portraitAttachmentId($playerId),
                'bio' => GuildProfile::bio($playerId),
                'characters' => $this->characters->allForOwner($playerId),
                'linked_character_ids' => $membership['character_ids'],
            ];
        }

        return $members;
    }

    private function url(string $campaignId): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/campaigns/' . $campaignId . '/players',
            home_url('/companion/')
        );
    }
}
