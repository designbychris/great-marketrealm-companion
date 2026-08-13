<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_Post;

defined('ABSPATH') || exit;

final class AdvancementHistoryRepository
{
    private const META_HISTORY =
        '_gmrc_advancement_history';

    /** @return array<int,array<string,mixed>> */
    public function all(
        CharacterId $characterId
    ): array {
        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return [];
        }

        $history = get_post_meta(
            $post->ID,
            self::META_HISTORY,
            true
        );

        return is_array($history)
            ? array_values($history)
            : [];
    }

    /** @param array<string,mixed> $entry */
    public function append(
        CharacterId $characterId,
        array $entry
    ): void {
        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return;
        }

        $history = $this->all(
            $characterId
        );

        $key = (string) (
            $entry['certification_key']
            ?? ''
        );

        foreach ($history as $existing) {
            if (
                is_array($existing)
                && (string) (
                    $existing['certification_key']
                    ?? ''
                ) === $key
            ) {
                return;
            }
        }

        $history[] = $entry;

        update_post_meta(
            $post->ID,
            self::META_HISTORY,
            array_slice($history, -20)
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
