<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Owner-scoped WordPress persistence for active class resource expenditure.
 */
final class ActiveClassResourceRepository
{
    private const META_RESOURCES =
        '_gmrc_active_class_resources';

    public function find(
        CharacterId $characterId
    ): ActiveClassResourceState {
        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return ActiveClassResourceState::fresh();
        }

        $stored = get_post_meta(
            $post->ID,
            self::META_RESOURCES,
            true
        );

        return ActiveClassResourceState::fromArray(
            is_array($stored)
                ? $stored
                : []
        );
    }

    public function save(
        CharacterId $characterId,
        ActiveClassResourceState $state
    ): void {
        if (! function_exists('update_post_meta')) {
            return;
        }

        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return;
        }

        update_post_meta(
            $post->ID,
            self::META_RESOURCES,
            $state->toArray()
        );
    }

    private function findPost(
        CharacterId $characterId
    ): ?WP_Post {
        if (! function_exists('get_posts')) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'gmrc_character',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'author' => get_current_user_id(),
            'meta_key' => '_gmrc_character_id',
            'meta_value' => $characterId->value(),
        ]);

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post
            ? $post
            : null;
    }
}
