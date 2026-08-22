<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Encounter;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class EncounterRepository
{
    public const POST_TYPE = 'gmrc_encounter';
    private const META_ID = '_gmrc_encounter_id';
    private const META_CAMPAIGN_ID = '_gmrc_encounter_campaign_id';
    private const META_SESSION_ID = '_gmrc_encounter_session_id';
    private const META_STATUS = '_gmrc_encounter_status';
    private const META_THREAT = '_gmrc_encounter_threat';
    private const META_LOCATION = '_gmrc_encounter_location';
    private const META_ADVERSARIES = '_gmrc_encounter_adversaries';
    private const META_NOTES = '_gmrc_encounter_notes';
    private const META_CHARACTERS = '_gmrc_encounter_character_ids';
    private const META_MONSTERS = '_gmrc_encounter_monster_groups';
    private const META_INITIATIVE = '_gmrc_encounter_initiative_table';

    public function __construct(private CampaignRepository $campaigns) {}

    /** @return Encounter[] */
    public function allForCampaign(Campaign $campaign): array
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $campaign->ownerId(),
            'meta_query' => [['key' => self::META_CAMPAIGN_ID, 'value' => $campaign->id()]],
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        return array_map(fn (WP_Post $post): Encounter => $this->map($post), $posts);
    }

    public function findForCampaign(string $encounterId, Campaign $campaign): ?Encounter
    {
        $post = $this->findPost($encounterId, $campaign);
        return $post instanceof WP_Post ? $this->map($post) : null;
    }

    /** @return array<string,mixed> */
    public function initiativeForCampaign(string $encounterId, Campaign $campaign): array
    {
        $post = $this->findPost($encounterId, $campaign);
        if (! $post instanceof WP_Post) { return []; }
        $state = get_post_meta($post->ID, self::META_INITIATIVE, true);
        return is_array($state) ? $state : [];
    }

    /** @param array<string,mixed> $state */
    public function saveInitiative(string $encounterId, Campaign $campaign, array $state): void
    {
        $post = $this->findPost($encounterId, $campaign);
        if (! $post instanceof WP_Post) { throw new RuntimeException('The Encounter could not be found for Initiative.'); }
        update_post_meta($post->ID, self::META_INITIATIVE, $state);
    }

    public function save(Encounter $encounter, Campaign $campaign): void
    {
        if ($encounter->campaignId() !== $campaign->id() || $encounter->ownerId() !== $campaign->ownerId()) {
            throw new RuntimeException('The Encounter does not belong to this Campaign Register.');
        }
        $existing = $this->findPost($encounter->id(), $campaign);
        $campaignPostId = $this->campaigns->postIdForOwner($campaign->id(), $campaign->ownerId());
        if ($campaignPostId === null) {
            throw new RuntimeException('The Campaign Register record could not be found.');
        }
        $payload = [
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $encounter->title(),
            'post_author' => $campaign->ownerId(),
            'post_parent' => $campaignPostId,
        ];
        if ($existing instanceof WP_Post) {
            $payload['ID'] = $existing->ID;
            $postId = wp_update_post($payload, true);
        } else {
            $postId = wp_insert_post($payload, true);
        }
        if (is_wp_error($postId) || (int) $postId < 1) {
            throw new RuntimeException('The Encounter could not be written to the Encounter Board.');
        }
        $meta = [
            self::META_ID => $encounter->id(),
            self::META_CAMPAIGN_ID => $campaign->id(),
            self::META_SESSION_ID => $encounter->sessionId(),
            self::META_STATUS => $encounter->status(),
            self::META_THREAT => $encounter->threat(),
            self::META_LOCATION => $encounter->location(),
            self::META_ADVERSARIES => $encounter->adversaries(),
            self::META_NOTES => $encounter->notes(),
            self::META_CHARACTERS => $encounter->characterIds(),
            self::META_MONSTERS => $encounter->monsterGroups(),
        ];
        foreach ($meta as $key => $value) { update_post_meta((int) $postId, $key, $value); }
    }

    private function findPost(string $encounterId, Campaign $campaign): ?WP_Post
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $campaign->ownerId(),
            'meta_query' => [
                ['key' => self::META_ID, 'value' => $encounterId],
                ['key' => self::META_CAMPAIGN_ID, 'value' => $campaign->id()],
            ],
        ]);
        if (count($posts) > 1) { throw new RuntimeException('The Encounter Board contains duplicate encounter records.'); }
        $post = $posts[0] ?? null;
        return $post instanceof WP_Post ? $post : null;
    }

    private function map(WP_Post $post): Encounter
    {
        $characters = get_post_meta($post->ID, self::META_CHARACTERS, true);
        $monsterGroups = get_post_meta($post->ID, self::META_MONSTERS, true);
        return Encounter::restore(
            (string) get_post_meta($post->ID, self::META_ID, true),
            (string) get_post_meta($post->ID, self::META_CAMPAIGN_ID, true),
            (int) $post->post_author,
            (string) $post->post_title,
            (string) get_post_meta($post->ID, self::META_SESSION_ID, true),
            (string) get_post_meta($post->ID, self::META_STATUS, true),
            (string) get_post_meta($post->ID, self::META_THREAT, true),
            (string) get_post_meta($post->ID, self::META_LOCATION, true),
            (string) get_post_meta($post->ID, self::META_ADVERSARIES, true),
            (string) get_post_meta($post->ID, self::META_NOTES, true),
            is_array($characters) ? array_values(array_map('strval', $characters)) : [],
            is_array($monsterGroups) ? array_values($monsterGroups) : []
        );
    }
}
