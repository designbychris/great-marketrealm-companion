<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignExpansionAccess;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class CampaignExpansionController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignExpansionAccess $expansions,
        private DungeonMasterAccess $access,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function update(string $id): RedirectResponse
    {
        $campaign = $this->campaign($id);

        if ($campaign->isArchived()) {
            throw new RuntimeException(
                'Archived campaigns cannot change their Almanac shelf.'
            );
        }

        $submitted = $_POST['expansions'] ?? [];
        $selected = is_array($submitted)
            ? array_values($submitted)
            : [];

        $this->expansions->saveCampaignSelection(
            $campaign,
            $selected
        );

        $count = count($this->expansions->activeForCampaign($campaign));
        $this->flash->success(
            $count === 1
                ? '1 Almanac is now shared with this Campaign.'
                : sprintf('%d Almanacs are now shared with this Campaign.', $count)
        );

        return $this->responses->redirect($this->url($campaign));
    }

    private function campaign(string $id): Campaign
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException(
                'Campaign Almanac controls are sealed to Dungeon Masters.'
            );
        }

        $campaign = $this->campaigns->findForOwner(
            $id,
            get_current_user_id()
        );

        if (! $campaign instanceof Campaign) {
            throw new RuntimeException(
                'Campaign not found in this Dungeon Master’s Register.'
            );
        }

        return $campaign;
    }

    private function url(Campaign $campaign): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/campaigns/' . $campaign->id(),
            home_url('/companion/')
        );
    }
}
