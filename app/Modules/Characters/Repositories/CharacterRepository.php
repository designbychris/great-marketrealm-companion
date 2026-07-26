<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Contracts\RepositoryInterface;

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
    public function all(): array
    {
        $posts = get_posts([
            'post_type'      => 'gmrc_character',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author'         => get_current_user_id(),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        return array_map(
            fn (\WP_Post $post): Character => $this->mapPost($post),
            $posts
        );
    }

    public function find(int $id): ?Character
    {
        $post = get_post($id);

        if (
            ! $post instanceof \WP_Post
            || $post->post_type !== 'gmrc_character'
        ) {
            return null;
        }

        return $this->mapPost($post);
    }

    public function delete(int $id): bool
    {
        return wp_delete_post($id, true) !== false;
    }

    public function create(Character $character): Character
    {
        // wp_insert_post() implementation goes here.
    }

    public function update(Character $character): Character
    {
        // wp_update_post() and update_post_meta() go here.
    }

    /**
     * Convert database data into a Character model.
     */
    protected function map(array $data): Character
    {
        return new Character(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            race: (string) ($data['race'] ?? ''),
            class: (string) ($data['class'] ?? ''),
            level: (int) ($data['level'] ?? 1),
        );
    }
}
