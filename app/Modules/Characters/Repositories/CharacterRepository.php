<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;

use GreatMarketrealmCompanion\Contracts\RepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Character Repository.
 *
 * Handles persistence for Character domain models.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class CharacterRepository implements RepositoryInterface
{
    protected string $postType = 'gmrc_character';

    /**
     * Retrieve all Characters belonging to the current user.
     *
     * @return Character[]
     */
    public function all(): array
    {
        $posts = get_posts([
            'post_type'      => $this->postType,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author'         => get_current_user_id(),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        return array_map(
            fn (WP_Post $post): Character => $this->mapPost($post),
            $posts
        );
    }

    /**
     * Find a Character by ID.
     */
    public function find(int $id): ?Character
    {
        $post = get_post($id);

        if (! $post instanceof WP_Post) {
            return null;
        }

        if ($post->post_type !== $this->postType) {
            return null;
        }

        if ((int) $post->post_author !== get_current_user_id()) {
            return null;
        }

        return $this->mapPost($post);
    }

    /**
     * Create a Character.
     */
    public function create(Character $character): Character
    {
        $postId = wp_insert_post(
            [
                'post_type'   => $this->postType,
                'post_status' => 'publish',
                'post_title'  => $character->name(),
                'post_author' => get_current_user_id(),
            ],
            true
        );

        if (is_wp_error($postId)) {
            throw new \RuntimeException(
                $postId->get_error_message()
            );
        }

        $this->saveMeta(
            (int) $postId,
            $character
        );

        return $this->find((int) $postId)
            ?? throw new \RuntimeException(
                'The character was created but could not be loaded.'
            );
    }

    /**
     * Update a Character.
     */
    public function update(Character $character): Character
    {
        $existing = $this->find($character->id());

        if ($existing === null) {
            throw new \RuntimeException(
                'The requested character could not be found.'
            );
        }

        $result = wp_update_post(
            [
                'ID'         => $character->id(),
                'post_title' => $character->name(),
            ],
            true
        );

        if (is_wp_error($result)) {
            throw new \RuntimeException(
                $result->get_error_message()
            );
        }

        $this->saveMeta(
            $character->id(),
            $character
        );

        return $this->find($character->id())
            ?? throw new \RuntimeException(
                'The character was updated but could not be loaded.'
            );
    }

    /**
     * Delete a Character.
     */
    public function delete(int $id): bool
    {
        if ($this->find($id) === null) {
            return false;
        }

        return wp_delete_post($id, true) instanceof WP_Post;
    }

    /**
     * Save Character metadata.
     */
    protected function saveMeta(
        int $postId,
        Character $character
    ): void {
        update_post_meta(
            $postId,
            '_gmrc_race',
            $character->race()
        );

        update_post_meta(
            $postId,
            '_gmrc_class',
            $character->class()
        );

        update_post_meta(
            $postId,
            '_gmrc_level',
            $character->level()
        );
    }

    /**
     * Convert a WordPress post into a Character model.
     */
    protected function mapPost(WP_Post $post): Character
    {
        return new Character(
            id: $post->ID,
            name: $post->post_title,
            race: (string) get_post_meta(
                $post->ID,
                '_gmrc_race',
                true
            ),
            class: (string) get_post_meta(
                $post->ID,
                '_gmrc_class',
                true
            ),
            level: max(
                1,
                (int) get_post_meta(
                    $post->ID,
                    '_gmrc_level',
                    true
                )
            ),
        );
    }
}
