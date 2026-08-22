<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use RuntimeException;

defined('ABSPATH') || exit;

final class GuildPortraitManager
{
    private const MAX_BYTES = 5 * MB_IN_BYTES;
    private const OWNER_META = '_gmrc_profile_portrait_owner';

    /** @param array<string,mixed> $file */
    public function upload(int $userId, array $file): int
    {
        if ($userId < 1 || empty($file['tmp_name']) || empty($file['name'])) {
            throw new RuntimeException('Choose a portrait image before visiting the Guild Illuminator.');
        }

        if ((int) ($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new RuntimeException('That portrait is too large. Please keep it below 5 MB.');
        }

        $checked = wp_check_filetype_and_ext(
            (string) $file['tmp_name'],
            (string) $file['name']
        );

        if (
            ! isset($checked['type'])
            || ! in_array(
                $checked['type'],
                ['image/jpeg', 'image/png', 'image/webp'],
                true
            )
        ) {
            throw new RuntimeException('Guild portraits must be JPG, PNG or WebP images.');
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachmentId = media_handle_upload('gmrc_profile_portrait', 0);

        if (is_wp_error($attachmentId)) {
            throw new RuntimeException('The Guild Illuminator could not frame that image. Please try another.');
        }

        update_post_meta((int) $attachmentId, self::OWNER_META, $userId);
        update_user_meta(
            $userId,
            GuildProfile::PORTRAIT_ATTACHMENT_META,
            (int) $attachmentId
        );

        return (int) $attachmentId;
    }

    public function remove(int $userId): void
    {
        if ($userId < 1) {
            return;
        }

        delete_user_meta(
            $userId,
            GuildProfile::PORTRAIT_ATTACHMENT_META
        );
    }
}
