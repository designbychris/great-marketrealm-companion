<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MarketPassRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\RedeemMarketPassRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignMembershipSynchronizer;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use RuntimeException;

defined('ABSPATH') || exit;

final class MarketPassController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private MarketPassRepository $passes,
        private DungeonMasterAccess $access,
        private CampaignMembershipSynchronizer $membershipSync,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function index(): string
    {
        $this->guardPlayer();

        return $this->views->render(View::make('dungeonmaster.market-pass.index', [
            'flash' => [
                'success' => $this->flash->get('success'),
                'error' => $this->flash->get('error'),
            ],
        ]));
    }

    public function redeem(RedeemMarketPassRequest $request): RedirectResponse
    {
        $this->guardPlayer();
        $playerId = get_current_user_id();
        $campaign = $this->passes->campaignForCode($request->code());

        if (! $campaign instanceof Campaign || $campaign->isArchived()) {
            $this->flash->error('That Market Pass is invalid, expired, revoked, or belongs to a closed campaign.');
            return $this->responses->redirect($this->playerUrl());
        }

        if ($campaign->ownerId() === $playerId) {
            $this->flash->error('A Dungeon Master cannot join their own campaign through a Market Pass.');
            return $this->responses->redirect($this->playerUrl());
        }

        if ($this->rosters->hasPlayer($campaign, $playerId)) {
            $this->membershipSync->synchronize($campaign);
            $this->flash->success('You are already signed into ' . $campaign->name() . '.');
            return $this->responses->redirect($this->activeCampaignsUrl());
        }

        $this->rosters->addPlayer($campaign, $playerId);
        $this->membershipSync->synchronize($campaign);
        $this->flash->success('Market Pass accepted. You have joined ' . $campaign->name() . '.');

        return $this->responses->redirect($this->activeCampaignsUrl());
    }

    public function issue(string $id): RedirectResponse
    {
        $campaign = $this->ownedCampaign($id);
        $this->assertActive($campaign);
        $pass = $this->passes->issue($campaign);
        $this->flash->success('A fresh Market Pass has been issued: ' . $pass->code());

        return $this->responses->redirect($this->rosterUrl($id));
    }

    public function revoke(string $id): RedirectResponse
    {
        $campaign = $this->ownedCampaign($id);
        $this->assertActive($campaign);
        $this->passes->revoke($campaign);
        $this->flash->success('The Market Pass has been revoked. Existing Campaign members remain on the roster.');

        return $this->responses->redirect($this->rosterUrl($id));
    }

    private function ownedCampaign(string $id): Campaign
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException('Market Pass controls are sealed to Dungeon Masters.');
        }

        $campaign = $this->campaigns->findForOwner($id, get_current_user_id());

        if (! $campaign instanceof Campaign) {
            throw new RuntimeException('Campaign not found in this Dungeon Master’s Register.');
        }

        return $campaign;
    }

    private function guardPlayer(): void
    {
        $userId = get_current_user_id();

        if ($userId < 1
            || ! user_can($userId, 'gmrc_access_companion')
            || GuildProfile::accountType($userId) !== AccountType::PLAYER) {
            status_header(403);
            throw new RuntimeException('Market Pass redemption is available to registered Guild Players.');
        }
    }

    private function assertActive(Campaign $campaign): void
    {
        if ($campaign->isArchived()) {
            throw new RuntimeException('Archived campaigns cannot issue or revoke Market Passes.');
        }
    }

    private function playerUrl(): string
    {
        return add_query_arg('gmrc_route', 'market-pass', home_url('/companion/'));
    }

    private function activeCampaignsUrl(): string
    {
        return add_query_arg('gmrc_route', 'active-campaigns', home_url('/companion/'));
    }

    private function rosterUrl(string $campaignId): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/campaigns/' . $campaignId . '/players',
            home_url('/companion/')
        );
    }
}
