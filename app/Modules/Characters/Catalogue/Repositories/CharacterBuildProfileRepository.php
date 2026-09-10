<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_Post;

defined('ABSPATH') || exit;

final class CharacterBuildProfileRepository
{
    private const META_CHARACTER_ID = '_gmrc_character_id';
    private const META_HERITAGE = '_gmrc_heritage';
    private const META_SUBCLASS = '_gmrc_subclass';
    private const META_EXPANSION_RACE = '_gmrc_expansion_race_id';
    private const META_EXPANSION_BACKGROUND = '_gmrc_expansion_background_id';
    private const META_EXPANSION_SUBCLASS = '_gmrc_expansion_subclass_id';

    public function save(CharacterId $id, string $heritage, string $subclass): void
    {
        $post = $this->findPost($id);
        if (! $post instanceof WP_Post) { return; }
        update_post_meta($post->ID, self::META_HERITAGE, sanitize_key($heritage));
        update_post_meta($post->ID, self::META_SUBCLASS, sanitize_key($subclass));
    }

    /** @return array{heritage:string,subclass:string} */
    public function find(CharacterId $id): array
    {
        $post = $this->findPost($id);
        if (! $post instanceof WP_Post) { return ['heritage'=>'','subclass'=>'']; }
        return [
            'heritage' => (string) get_post_meta($post->ID, self::META_HERITAGE, true),
            'subclass' => (string) get_post_meta($post->ID, self::META_SUBCLASS, true),
        ];
    }


    /**
     * Persist sourcebook provenance without copying sourcebook mechanics.
     *
     * @param array{race?:?string,background?:?string,subclass?:?string} $references
     */
    public function saveExpansionReferences(CharacterId $id, array $references): void
    {
        $post = $this->findPost($id);
        if (! $post instanceof WP_Post) {
            return;
        }

        foreach ([
            'race' => self::META_EXPANSION_RACE,
            'background' => self::META_EXPANSION_BACKGROUND,
            'subclass' => self::META_EXPANSION_SUBCLASS,
        ] as $key => $metaKey) {
            $value = trim((string) ($references[$key] ?? ''));
            if ($value === '') {
                if (function_exists('delete_post_meta')) {
                    delete_post_meta($post->ID, $metaKey);
                }
                continue;
            }

            update_post_meta($post->ID, $metaKey, $value);
        }
    }

    /** @return array{race:string,background:string,subclass:string} */
    public function expansionReferences(CharacterId $id): array
    {
        $post = $this->findPost($id);
        if (! $post instanceof WP_Post) {
            return ['race' => '', 'background' => '', 'subclass' => ''];
        }

        return [
            'race' => (string) get_post_meta($post->ID, self::META_EXPANSION_RACE, true),
            'background' => (string) get_post_meta($post->ID, self::META_EXPANSION_BACKGROUND, true),
            'subclass' => (string) get_post_meta($post->ID, self::META_EXPANSION_SUBCLASS, true),
        ];
    }

    private function findPost(CharacterId $id): ?WP_Post
    {
        if (! function_exists('get_posts')) {
            return null;
        }

        $posts = get_posts([
            'post_type'=>'gmrc_character','post_status'=>'publish','posts_per_page'=>1,
            'author'=>get_current_user_id(),'meta_key'=>self::META_CHARACTER_ID,'meta_value'=>$id->value(),
        ]);
        return ($posts[0] ?? null) instanceof WP_Post ? $posts[0] : null;
    }
}
