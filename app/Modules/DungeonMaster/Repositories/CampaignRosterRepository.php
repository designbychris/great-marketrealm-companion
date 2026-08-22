<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use RuntimeException;

defined('ABSPATH') || exit;

final class CampaignRosterRepository
{
    private const META_ROSTER = '_gmrc_campaign_roster';

    public function __construct(
        private CampaignRepository $campaigns
    ) {
    }

    /**
     * @return array<int,array{player_id:int,character_ids:array<int,string>}>
     */
    public function members(Campaign $campaign): array
    {
        $roster = $this->read($campaign);
        $members = [];

        foreach ($roster as $playerId => $entry) {
            $members[] = [
                'player_id' => (int) $playerId,
                'character_ids' => $this->characterIds($entry),
            ];
        }

        return $members;
    }

    public function hasPlayer(Campaign $campaign, int $playerId): bool
    {
        return array_key_exists(
            (string) $playerId,
            $this->read($campaign)
        );
    }

    public function addPlayer(Campaign $campaign, int $playerId): void
    {
        $roster = $this->read($campaign);
        $key = (string) $playerId;

        if (! isset($roster[$key])) {
            $roster[$key] = ['character_ids' => []];
            $this->write($campaign, $roster);
        }
    }

    public function removePlayer(Campaign $campaign, int $playerId): void
    {
        $roster = $this->read($campaign);
        unset($roster[(string) $playerId]);
        $this->write($campaign, $roster);
    }

    public function attachCharacter(
        Campaign $campaign,
        int $playerId,
        string $characterId
    ): void {
        $roster = $this->read($campaign);
        $key = (string) $playerId;

        if (! isset($roster[$key])) {
            throw new RuntimeException(
                'The Player must belong to this Campaign Roster first.'
            );
        }

        $ids = $this->characterIds($roster[$key]);
        $ids[] = $characterId;
        $roster[$key]['character_ids'] = array_values(array_unique($ids));
        $this->write($campaign, $roster);
    }

    public function detachCharacter(
        Campaign $campaign,
        int $playerId,
        string $characterId
    ): void {
        $roster = $this->read($campaign);
        $key = (string) $playerId;

        if (! isset($roster[$key])) {
            return;
        }

        $roster[$key]['character_ids'] = array_values(
            array_filter(
                $this->characterIds($roster[$key]),
                static fn (string $id): bool => $id !== $characterId
            )
        );

        $this->write($campaign, $roster);
    }

    /** @return array<string,array{character_ids?:array<int,string>}> */
    private function read(Campaign $campaign): array
    {
        $postId = $this->postId($campaign);
        $stored = get_post_meta($postId, self::META_ROSTER, true);

        return is_array($stored) ? $stored : [];
    }

    /** @param array<string,mixed> $roster */
    private function write(Campaign $campaign, array $roster): void
    {
        update_post_meta(
            $this->postId($campaign),
            self::META_ROSTER,
            $roster
        );
    }

    private function postId(Campaign $campaign): int
    {
        $postId = $this->campaigns->postIdForOwner(
            $campaign->id(),
            $campaign->ownerId()
        );

        if ($postId === null) {
            throw new RuntimeException(
                'The Campaign Register record could not be found.'
            );
        }

        return $postId;
    }

    /** @return array<int,string> */
    private function characterIds(mixed $entry): array
    {
        if (! is_array($entry)) {
            return [];
        }

        $ids = $entry['character_ids'] ?? [];

        if (! is_array($ids)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map('strval', $ids),
                static fn (string $id): bool => $id !== ''
            )
        );
    }
}
