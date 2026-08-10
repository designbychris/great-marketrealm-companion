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

    private function findPost(CharacterId $id): ?WP_Post
    {
        $posts = get_posts([
            'post_type'=>'gmrc_character','post_status'=>'publish','posts_per_page'=>1,
            'author'=>get_current_user_id(),'meta_key'=>self::META_CHARACTER_ID,'meta_value'=>$id->value(),
        ]);
        return ($posts[0] ?? null) instanceof WP_Post ? $posts[0] : null;
    }
}
