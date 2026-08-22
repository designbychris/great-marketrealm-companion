<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Monster;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class MonsterRepository
{
    public const POST_TYPE = 'gmrc_monster';

    private const META_ID = '_gmrc_monster_id';
    private const META_TYPE = '_gmrc_monster_type';
    private const META_SIZE = '_gmrc_monster_size';
    private const META_AC = '_gmrc_monster_armor_class';
    private const META_HP = '_gmrc_monster_max_hp';
    private const META_SPEED = '_gmrc_monster_speed';
    private const META_STR = '_gmrc_monster_strength';
    private const META_DEX = '_gmrc_monster_dexterity';
    private const META_CON = '_gmrc_monster_constitution';
    private const META_INT = '_gmrc_monster_intelligence';
    private const META_WIS = '_gmrc_monster_wisdom';
    private const META_CHA = '_gmrc_monster_charisma';
    private const META_CHALLENGE = '_gmrc_monster_challenge';
    private const META_TRAITS = '_gmrc_monster_traits';
    private const META_ACTIONS = '_gmrc_monster_actions';
    private const META_NOTES = '_gmrc_monster_notes';
    private const META_STATUS = '_gmrc_monster_status';

    /** @return Monster[] */
    public function allForOwner(int $ownerId): array
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $ownerId,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        return array_map(
            fn (WP_Post $post): Monster => $this->map($post),
            $posts
        );
    }

    /** @return Monster[] */
    public function activeForOwner(int $ownerId): array
    {
        return array_values(array_filter(
            $this->allForOwner($ownerId),
            static fn (Monster $monster): bool => ! $monster->isArchived()
        ));
    }

    public function findForOwner(string $monsterId, int $ownerId): ?Monster
    {
        $post = $this->findPost($monsterId, $ownerId);
        return $post instanceof WP_Post ? $this->map($post) : null;
    }

    public function save(Monster $monster): void
    {
        $existing = $this->findPost($monster->id(), $monster->ownerId());
        $payload = [
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $monster->name(),
            'post_author' => $monster->ownerId(),
        ];

        if ($existing instanceof WP_Post) {
            $payload['ID'] = $existing->ID;
            $postId = wp_update_post($payload, true);
        } else {
            $postId = wp_insert_post($payload, true);
        }

        if (is_wp_error($postId) || (int) $postId < 1) {
            throw new RuntimeException('The creature could not be written to the Monster Ledger.');
        }

        $meta = [
            self::META_ID => $monster->id(),
            self::META_TYPE => $monster->creatureType(),
            self::META_SIZE => $monster->size(),
            self::META_AC => $monster->armorClass(),
            self::META_HP => $monster->maxHp(),
            self::META_SPEED => $monster->speed(),
            self::META_STR => $monster->strength(),
            self::META_DEX => $monster->dexterity(),
            self::META_CON => $monster->constitution(),
            self::META_INT => $monster->intelligence(),
            self::META_WIS => $monster->wisdom(),
            self::META_CHA => $monster->charisma(),
            self::META_CHALLENGE => $monster->challenge(),
            self::META_TRAITS => $monster->traits(),
            self::META_ACTIONS => $monster->actions(),
            self::META_NOTES => $monster->notes(),
            self::META_STATUS => $monster->status(),
        ];

        foreach ($meta as $key => $value) {
            update_post_meta((int) $postId, $key, $value);
        }
    }

    private function findPost(string $monsterId, int $ownerId): ?WP_Post
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $ownerId,
            'meta_key' => self::META_ID,
            'meta_value' => $monsterId,
        ]);

        if (count($posts) > 1) {
            throw new RuntimeException('The Monster Ledger contains duplicate creature records.');
        }

        $post = $posts[0] ?? null;
        return $post instanceof WP_Post ? $post : null;
    }

    private function map(WP_Post $post): Monster
    {
        return Monster::restore(
            (string) get_post_meta($post->ID, self::META_ID, true),
            (int) $post->post_author,
            (string) $post->post_title,
            (string) get_post_meta($post->ID, self::META_TYPE, true),
            (string) get_post_meta($post->ID, self::META_SIZE, true),
            max(0, (int) get_post_meta($post->ID, self::META_AC, true)),
            max(1, (int) get_post_meta($post->ID, self::META_HP, true)),
            (string) get_post_meta($post->ID, self::META_SPEED, true),
            max(1, (int) get_post_meta($post->ID, self::META_STR, true)),
            max(1, (int) get_post_meta($post->ID, self::META_DEX, true)),
            max(1, (int) get_post_meta($post->ID, self::META_CON, true)),
            max(1, (int) get_post_meta($post->ID, self::META_INT, true)),
            max(1, (int) get_post_meta($post->ID, self::META_WIS, true)),
            max(1, (int) get_post_meta($post->ID, self::META_CHA, true)),
            (string) get_post_meta($post->ID, self::META_CHALLENGE, true),
            (string) get_post_meta($post->ID, self::META_TRAITS, true),
            (string) get_post_meta($post->ID, self::META_ACTIONS, true),
            (string) get_post_meta($post->ID, self::META_NOTES, true),
            (string) get_post_meta($post->ID, self::META_STATUS, true)
        );
    }
}
