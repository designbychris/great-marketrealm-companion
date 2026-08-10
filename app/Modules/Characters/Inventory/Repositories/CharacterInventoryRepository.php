<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_Post;

defined('ABSPATH') || exit;

/** WordPress metadata persistence for character inventory. */
final class CharacterInventoryRepository
{
    private const META_INVENTORY = '_gmrc_inventory';

    public function find(CharacterId $characterId): CharacterInventory
    {
        $post = $this->findPost($characterId);
        if (! $post instanceof WP_Post) { return CharacterInventory::empty(); }
        $stored = get_post_meta($post->ID, self::META_INVENTORY, true);
        return is_array($stored) ? CharacterInventory::fromArray($stored) : CharacterInventory::empty();
    }

    public function save(CharacterId $characterId, CharacterInventory $inventory): void
    {
        if (! function_exists('update_post_meta')) {
            return;
        }

        $post = $this->findPost($characterId);
        if (! $post instanceof WP_Post) { return; }
        update_post_meta($post->ID, self::META_INVENTORY, $inventory->toArray());
    }

    private function findPost(CharacterId $characterId): ?WP_Post
    {
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
        return $post instanceof WP_Post ? $post : null;
    }
}
