<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use RuntimeException;

final class CampaignTabletopLinkRepository
{
    private const META_TABLETOP_ID = '_gmrc_campaign_tabletop_id';

    public function __construct(private CampaignRepository $campaigns) {}

    public function tableId(Campaign $campaign): string
    {
        $postId = $this->campaigns->postIdForOwner($campaign->id(), $campaign->ownerId());
        if ($postId === null) {
            return '';
        }
        return trim((string) get_post_meta($postId, self::META_TABLETOP_ID, true));
    }

    public function campaignForTable(string $tableId, int $ownerId): ?Campaign
    {
        $tableId = trim($tableId);
        if ($tableId === '' || $ownerId < 1) {
            return null;
        }

        foreach ($this->campaigns->allForOwner($ownerId) as $campaign) {
            if ($this->tableId($campaign) === $tableId) {
                return $campaign;
            }
        }
        return null;
    }

    public function link(Campaign $campaign, string $tableId): void
    {
        $tableId = trim($tableId);
        if ($tableId === '') {
            throw new RuntimeException('A Tabletop ID is required to link this Campaign.');
        }

        foreach ($this->campaigns->allForOwner($campaign->ownerId()) as $other) {
            if ($other->id() === $campaign->id()) {
                continue;
            }
            if ($this->tableId($other) === $tableId) {
                $otherPostId = $this->campaigns->postIdForOwner($other->id(), $other->ownerId());
                if ($otherPostId !== null) {
                    delete_post_meta($otherPostId, self::META_TABLETOP_ID);
                }
            }
        }

        $postId = $this->campaigns->postIdForOwner($campaign->id(), $campaign->ownerId());
        if ($postId === null) {
            throw new RuntimeException('The Campaign Register record could not be found.');
        }
        update_post_meta($postId, self::META_TABLETOP_ID, $tableId);
    }
}
