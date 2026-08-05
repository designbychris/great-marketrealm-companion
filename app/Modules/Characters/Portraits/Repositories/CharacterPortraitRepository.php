<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitAttachmentId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitMode;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * WordPress Character Portrait Repository.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class CharacterPortraitRepository implements CharacterPortraitRepositoryInterface
{
    private const META_CHARACTER_ID =
        '_gmrc_character_id';

    private const META_MODE =
        '_gmrc_portrait_mode';

    private const META_RECIPE =
        '_gmrc_portrait_recipe';

    private const META_ATTACHMENT =
        '_gmrc_portrait_attachment';

    public function find(
        CharacterId $characterId
    ): ?CharacterPortrait {
        $post = $this->findCharacterPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return null;
        }

        $modeValue = (string) get_post_meta(
            $post->ID,
            self::META_MODE,
            true
        );

        if ($modeValue === '') {
            return null;
        }

        $mode = PortraitMode::fromString(
            $modeValue
        );

        $recipe = $this->mapRecipe(
            $post->ID
        );

        if ($mode->isGenerated()) {
            return $recipe instanceof PortraitRecipe
                ? CharacterPortrait::generated(
                    $recipe
                )
                : null;
        }

        if ($mode->isCustom()) {
            $attachmentId = (int) get_post_meta(
                $post->ID,
                self::META_ATTACHMENT,
                true
            );

            if ($attachmentId < 1) {
                return $recipe instanceof PortraitRecipe
                    ? CharacterPortrait::generated(
                        $recipe
                    )
                    : null;
            }

            return CharacterPortrait::custom(
                PortraitAttachmentId::fromInt(
                    $attachmentId
                ),
                $recipe
            );
        }

        return CharacterPortrait::none();
    }

    public function save(
        CharacterId $characterId,
        CharacterPortrait $portrait
    ): void {
        $post = $this->findCharacterPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            throw new RuntimeException(
                'The portrait cannot be saved because its Character was not found.'
            );
        }

        update_post_meta(
            $post->ID,
            self::META_MODE,
            $portrait->mode()->value()
        );

        $recipe = $portrait->recipe();

        if ($recipe instanceof PortraitRecipe) {
            update_post_meta(
                $post->ID,
                self::META_RECIPE,
                wp_json_encode(
                    $recipe->toArray()
                )
            );
        } else {
            delete_post_meta(
                $post->ID,
                self::META_RECIPE
            );
        }

        $attachmentId =
            $portrait->attachmentId();

        if (
            $attachmentId
                instanceof PortraitAttachmentId
        ) {
            update_post_meta(
                $post->ID,
                self::META_ATTACHMENT,
                $attachmentId->value()
            );
        } else {
            delete_post_meta(
                $post->ID,
                self::META_ATTACHMENT
            );
        }
    }

    public function delete(
        CharacterId $characterId
    ): void {
        $post = $this->findCharacterPost(
            $characterId
        );

        if (! $post instanceof WP_Post) {
            return;
        }

        delete_post_meta(
            $post->ID,
            self::META_MODE
        );

        delete_post_meta(
            $post->ID,
            self::META_RECIPE
        );

        delete_post_meta(
            $post->ID,
            self::META_ATTACHMENT
        );
    }

    private function mapRecipe(
        int $postId
    ): ?PortraitRecipe {
        $stored = get_post_meta(
            $postId,
            self::META_RECIPE,
            true
        );

        if (
            ! is_string($stored)
            || $stored === ''
        ) {
            return null;
        }

        $decoded = json_decode(
            $stored,
            true
        );

        if (! is_array($decoded)) {
            return null;
        }

        try {
            return PortraitRecipe::fromArray(
                $decoded
            );
        } catch (\Throwable) {
            return null;
        }
    }

    private function findCharacterPost(
        CharacterId $characterId
    ): ?WP_Post {
        $posts = get_posts([
            'post_type' => 'gmrc_character',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'author' => get_current_user_id(),
            'meta_key' => self::META_CHARACTER_ID,
            'meta_value' => $characterId->value(),
        ]);

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post
            ? $post
            : null;
    }
}
