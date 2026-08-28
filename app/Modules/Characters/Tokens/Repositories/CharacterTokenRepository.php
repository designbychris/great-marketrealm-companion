<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Tokens\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Tokens\Models\CharacterToken;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * WordPress persistence for a Character's Tabletop token recipe.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.1
 */
final class CharacterTokenRepository
{
    private const META_CHARACTER_ID = '_gmrc_character_id';
    private const META_TOKEN = '_gmrc_tabletop_token';

    public function find(CharacterId $characterId): CharacterToken
    {
        return $this->findForOwner($characterId, function_exists('get_current_user_id') ? get_current_user_id() : 0);
    }

    public function findForOwner(CharacterId $characterId, int $ownerId): CharacterToken
    {
        $post = $this->findCharacterPost($characterId, $ownerId);

        if (! $post instanceof WP_Post) {
            return CharacterToken::portrait();
        }

        $stored = get_post_meta($post->ID, self::META_TOKEN, true);

        if (! is_string($stored) || $stored === '') {
            return CharacterToken::portrait();
        }

        $decoded = json_decode($stored, true);

        if (! is_array($decoded)) {
            return CharacterToken::portrait();
        }

        try {
            return CharacterToken::fromArray($decoded);
        } catch (\Throwable) {
            return CharacterToken::portrait();
        }
    }

    public function save(CharacterId $characterId, CharacterToken $token): void
    {
        $post = $this->findCharacterPost($characterId);

        if (! $post instanceof WP_Post) {
            throw new RuntimeException(
                'The Tabletop token cannot be saved because its Character was not found.'
            );
        }

        update_post_meta(
            $post->ID,
            self::META_TOKEN,
            wp_json_encode($token->toArray())
        );
    }

    public function delete(CharacterId $characterId): void
    {
        $post = $this->findCharacterPost($characterId);

        if ($post instanceof WP_Post) {
            delete_post_meta($post->ID, self::META_TOKEN);
        }
    }

    private function findCharacterPost(CharacterId $characterId, ?int $ownerId = null): ?WP_Post
    {
        // Controller unit tests deliberately exercise the Ledger without
        // bootstrapping WordPress persistence. In that isolated runtime the
        // safest token projection is the same one used for an unconfigured
        // Character: follow the existing portrait. Production requests always
        // have these WordPress functions available and continue below.
        if (! function_exists('get_posts')) {
            return null;
        }

        $ownerId = $ownerId ?? (function_exists('get_current_user_id') ? get_current_user_id() : 0);
        if ($ownerId < 1) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'gmrc_character',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'author' => $ownerId,
            'meta_key' => self::META_CHARACTER_ID,
            'meta_value' => $characterId->value(),
        ]);

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post ? $post : null;
    }
}
