<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Persist the Almanacs a Dungeon Master has shared with one Campaign.
 *
 * This stores canonical expansion keys only. GMREXP continues to own the
 * expansion definitions and site-level activation state.
 */
final class CampaignExpansionRepository
{
    private const META_EXPANSIONS = '_gmrc_campaign_expansions';

    public function __construct(
        private CampaignRepository $campaigns
    ) {
    }

    /** @return string[] */
    public function configured(Campaign $campaign): array
    {
        $stored = get_post_meta(
            $this->postId($campaign),
            self::META_EXPANSIONS,
            true
        );

        if (! is_array($stored)) {
            return [];
        }

        $keys = [];
        foreach ($stored as $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $key = $this->key((string) $value);
            if ($key !== '') {
                $keys[$key] = true;
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        return $keys;
    }

    /** @param string[] $expansionKeys */
    public function save(Campaign $campaign, array $expansionKeys): void
    {
        $keys = [];
        foreach ($expansionKeys as $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $key = $this->key((string) $value);
            if ($key !== '') {
                $keys[$key] = true;
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        update_post_meta(
            $this->postId($campaign),
            self::META_EXPANSIONS,
            $keys
        );
    }

    public function shares(Campaign $campaign, string $expansionKey): bool
    {
        return in_array(
            $this->key($expansionKey),
            $this->configured($campaign),
            true
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

    private function key(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
        $value = is_string($value) ? preg_replace('/-+/', '-', $value) : '';

        return is_string($value) ? trim($value, '-') : '';
    }
}
