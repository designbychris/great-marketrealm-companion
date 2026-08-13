<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\PendingAdvancement;
use WP_Post;

defined('ABSPATH') || exit;

final class PendingAdvancementRepository
{
    private const META_PENDING_ADVANCEMENT =
        '_gmrc_pending_advancement';

    public function find(
        CharacterId $characterId
    ): ?PendingAdvancement {
        $post = $this->findPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return null;
        }

        $stored = get_post_meta(
            $post->ID,
            self::META_PENDING_ADVANCEMENT,
            true
        );

        return is_array($stored)
            ? PendingAdvancement::fromArray(
                $characterId,
                $stored
            )
            : null;
    }

    public function resumeOrBegin(
        CharacterId $characterId,
        int $fromLevel,
        int $targetLevel
    ): PendingAdvancement {
        $pending = $this->find(
            $characterId
        );

        if (
            $pending instanceof PendingAdvancement
            && $pending->matches(
                $fromLevel,
                $targetLevel
            )
        ) {
            return $pending;
        }

        $pending = PendingAdvancement::begin(
            $characterId,
            $fromLevel,
            $targetLevel
        );

        $this->save($pending);

        return $pending;
    }

    public function save(
        PendingAdvancement $pending
    ): void {
        if (! function_exists('update_post_meta')) {
            return;
        }

        $post = $this->findPost(
            $pending->characterId()
        );

        if (! $post instanceof WP_Post) {
            return;
        }

        update_post_meta(
            $post->ID,
            self::META_PENDING_ADVANCEMENT,
            $pending->toArray()
        );
    }

    /**
     * @param array<int,string> $selections
     */
    public function recordChoice(
        CharacterId $characterId,
        int $fromLevel,
        int $targetLevel,
        string $choiceKey,
        array $selections
    ): PendingAdvancement {
        $pending = $this->resumeOrBegin(
            $characterId,
            $fromLevel,
            $targetLevel
        );

        $pending->recordChoice(
            $choiceKey,
            $selections
        );

        $this->save($pending);

        return $pending;
    }

    public function clear(
        CharacterId $characterId
    ): void {
        if (! function_exists('delete_post_meta')) {
            return;
        }

        $post = $this->findPost(
            $characterId
        );

        if ($post instanceof WP_Post) {
            delete_post_meta(
                $post->ID,
                self::META_PENDING_ADVANCEMENT
            );
        }
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
