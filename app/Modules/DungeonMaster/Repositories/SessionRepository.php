<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Session;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class SessionRepository
{
    public const POST_TYPE = 'gmrc_session';
    private const META_ID = '_gmrc_session_id';
    private const META_CAMPAIGN_ID = '_gmrc_session_campaign_id';
    private const META_NUMBER = '_gmrc_session_number';
    private const META_DATE = '_gmrc_session_date';
    private const META_STATUS = '_gmrc_session_status';
    private const META_PREP = '_gmrc_session_prep_notes';
    private const META_RECAP = '_gmrc_session_recap';
    private const META_ATTENDANCE = '_gmrc_session_attendance';
    private const META_TABLETOP_TABLE_ID = '_gmrc_session_tabletop_table_id';
    private const META_TABLETOP_SESSION_ID = '_gmrc_session_tabletop_session_id';
    private const META_STARTED_AT = '_gmrc_session_started_at';
    private const META_ENDED_AT = '_gmrc_session_ended_at';
    private const META_DURATION_SECONDS = '_gmrc_session_duration_seconds';
    private const META_CONTRIBUTIONS = '_gmrc_session_tabletop_contributions';

    public function __construct(private CampaignRepository $campaigns) {}

    /** @return Session[] */
    public function allForCampaign(Campaign $campaign): array
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $campaign->ownerId(),
            'meta_query' => [[
                'key' => self::META_CAMPAIGN_ID,
                'value' => $campaign->id(),
            ]],
            'meta_key' => self::META_NUMBER,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        ]);

        return array_map(fn (WP_Post $post): Session => $this->map($post), $posts);
    }

    public function findForCampaign(string $sessionId, Campaign $campaign): ?Session
    {
        $post = $this->findPost($sessionId, $campaign);
        return $post instanceof WP_Post ? $this->map($post) : null;
    }

    public function findByTabletopSessionId(string $tabletopSessionId, Campaign $campaign): ?Session
    {
        if (trim($tabletopSessionId) === '') {
            return null;
        }
        foreach ($this->allForCampaign($campaign) as $session) {
            if ($session->tabletopSessionId() === $tabletopSessionId) {
                return $session;
            }
        }
        return null;
    }

    public function findUnlinkedByNumber(int $number, Campaign $campaign): ?Session
    {
        foreach ($this->allForCampaign($campaign) as $session) {
            if ($session->number() === $number && ! $session->isTabletopSession() && $session->status() !== Session::STATUS_CANCELLED) {
                return $session;
            }
        }
        return null;
    }

    public function save(Session $session, Campaign $campaign): void
    {
        if ($session->campaignId() !== $campaign->id() || $session->ownerId() !== $campaign->ownerId()) {
            throw new RuntimeException('The Session does not belong to this Campaign Register.');
        }

        $existing = $this->findPost($session->id(), $campaign);
        $campaignPostId = $this->campaigns->postIdForOwner($campaign->id(), $campaign->ownerId());
        if ($campaignPostId === null) {
            throw new RuntimeException('The Campaign Register record could not be found.');
        }

        $payload = [
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $session->title(),
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
            throw new RuntimeException('The Session could not be written to the Session Ledger.');
        }

        $meta = [
            self::META_ID => $session->id(),
            self::META_CAMPAIGN_ID => $campaign->id(),
            self::META_NUMBER => $session->number(),
            self::META_DATE => $session->scheduledDate(),
            self::META_STATUS => $session->status(),
            self::META_PREP => $session->prepNotes(),
            self::META_RECAP => $session->recap(),
            self::META_ATTENDANCE => $session->attendance(),
            self::META_TABLETOP_TABLE_ID => $session->tabletopTableId(),
            self::META_TABLETOP_SESSION_ID => $session->tabletopSessionId(),
            self::META_STARTED_AT => $session->startedAt(),
            self::META_ENDED_AT => $session->endedAt(),
            self::META_DURATION_SECONDS => $session->durationSeconds(),
            self::META_CONTRIBUTIONS => $session->contributions(),
        ];
        foreach ($meta as $key => $value) {
            update_post_meta((int) $postId, $key, $value);
        }
    }

    private function findPost(string $sessionId, Campaign $campaign): ?WP_Post
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $campaign->ownerId(),
            'meta_query' => [
                ['key' => self::META_ID, 'value' => $sessionId],
                ['key' => self::META_CAMPAIGN_ID, 'value' => $campaign->id()],
            ],
        ]);
        if (count($posts) > 1) {
            throw new RuntimeException('The Session Ledger contains duplicate session records.');
        }
        $post = $posts[0] ?? null;
        return $post instanceof WP_Post ? $post : null;
    }

    private function map(WP_Post $post): Session
    {
        $attendance = get_post_meta($post->ID, self::META_ATTENDANCE, true);
        $contributions = get_post_meta($post->ID, self::META_CONTRIBUTIONS, true);
        return Session::restore(
            (string) get_post_meta($post->ID, self::META_ID, true),
            (string) get_post_meta($post->ID, self::META_CAMPAIGN_ID, true),
            (int) $post->post_author,
            max(1, (int) get_post_meta($post->ID, self::META_NUMBER, true)),
            (string) $post->post_title,
            (string) get_post_meta($post->ID, self::META_DATE, true),
            (string) get_post_meta($post->ID, self::META_STATUS, true),
            (string) get_post_meta($post->ID, self::META_PREP, true),
            (string) get_post_meta($post->ID, self::META_RECAP, true),
            is_array($attendance) ? $attendance : [],
            (string) get_post_meta($post->ID, self::META_TABLETOP_TABLE_ID, true),
            (string) get_post_meta($post->ID, self::META_TABLETOP_SESSION_ID, true),
            (string) get_post_meta($post->ID, self::META_STARTED_AT, true),
            (string) get_post_meta($post->ID, self::META_ENDED_AT, true),
            max(0, (int) get_post_meta($post->ID, self::META_DURATION_SECONDS, true)),
            is_array($contributions) ? $contributions : []
        );
    }
}
