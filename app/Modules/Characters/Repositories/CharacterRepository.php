<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Character Repository.
 *
 * Handles persistence of Character entities.
 */
final class CharacterRepository implements CharacterRepositoryInterface
{
    private string $postType = 'gmrc_character';

    /**
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

    public function find(
        CharacterId $id
    ): ?Character {
        $post = get_post($id->value());

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

    public function save(
        Character $character
    ): void {
        $postId = $character->id()->value();

        if ($postId === 0) {

            $postId = wp_insert_post([
                'post_type'   => $this->postType,
                'post_status' => 'publish',
                'post_title'  => $character->name()->value(),
                'post_author' => get_current_user_id(),
            ], true);

        } else {

            $postId = wp_update_post([
                'ID'         => $postId,
                'post_title' => $character->name()->value(),
            ], true);

        }

        if (is_wp_error($postId)) {
            throw new RuntimeException(
                $postId->get_error_message()
            );
        }

        $this->saveMeta(
            (int) $postId,
            $character
        );
    }

    public function delete(
        CharacterId $id
    ): void {
        wp_delete_post(
            $id->value(),
            true
        );
    }

    protected function saveMeta(
        int $postId,
        Character $character
    ): void {

        update_post_meta(
            $postId,
            '_gmrc_level',
            $character
                ->level()
                ->value()
        );

        update_post_meta(
            $postId,
            '_gmrc_experience',
            $character
                ->experience()
                ->value()
        );

        /*
         * TODO
         *
         * Race Value Object
         * CharacterClass Value Object
         *
         * update_post_meta(...)
         */
    }

    protected function mapPost(
        WP_Post $post
    ): Character {

        return Character::reconstitute(
            CharacterId::fromString((string) $post->ID),
            CharacterName::fromString($post->post_title),
            Level::fromInt(
                max(
                    1,
                    (int) get_post_meta(
                        $post->ID,
                        '_gmrc_level',
                        true
                    )
                )
            ),
            Experience::fromInt(
                (int) get_post_meta(
                    $post->ID,
                    '_gmrc_experience',
                    true
                )
            )
        );
    }
}
