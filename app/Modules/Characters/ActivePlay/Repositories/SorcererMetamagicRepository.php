<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Owner-scoped persistence for certified Sorcerer Metamagic selections.
 *
 * These are durable character choices, not expendable active-play resources.
 */
final class SorcererMetamagicRepository
{
    private const META =
        '_gmrc_sorcerer_metamagic';

    /** @return array<int,string> */
    public function find(
        CharacterId $characterId
    ): array {
        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return [];
        }

        $stored = get_post_meta(
            $post->ID,
            self::META,
            true
        );

        if (! is_array($stored)) {
            return [];
        }

        return $this->normalise(
            $stored
        );
    }

    /**
     * @param array<int,string> $choices
     */
    public function save(
        CharacterId $characterId,
        array $choices
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
            self::META,
            $this->normalise(
                $choices
            )
        );
    }

    /**
     * @param array<int,mixed> $choices
     * @return array<int,string>
     */
    private function normalise(
        array $choices
    ): array {
        $normalised = [];

        foreach ($choices as $choice) {
            $key = sanitize_key(
                (string) $choice
            );

            if (
                $key === ''
                || in_array(
                    $key,
                    $normalised,
                    true
                )
            ) {
                continue;
            }

            $normalised[] = $key;
        }

        sort($normalised);

        return $normalised;
    }

    private function findPost(
        CharacterId $characterId
    ): ?WP_Post {
        if (! function_exists('get_posts')) {
            return null;
        }

        $posts = get_posts([
            'post_type' =>
                'gmrc_character',
            'post_status' =>
                'publish',
            'posts_per_page' => 1,
            'author' =>
                get_current_user_id(),
            'meta_key' =>
                '_gmrc_character_id',
            'meta_value' =>
                $characterId->value(),
        ]);

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post
            ? $post
            : null;
    }
}
