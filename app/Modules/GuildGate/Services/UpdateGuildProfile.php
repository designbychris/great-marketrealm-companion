<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use RuntimeException;

use function sanitize_email;
use function sanitize_text_field;
use function update_user_meta;
use function wp_update_user;
use function is_email;
use function email_exists;
use function is_wp_error;

defined('ABSPATH') || exit;

final class UpdateGuildProfile
{
    public const BIO_META = 'gmrc_profile_bio';

    public function handle(
        int $userId,
        string $displayName,
        string $email,
        string $bio
    ): void {
        $displayName = sanitize_text_field($displayName);
        $email = sanitize_email($email);
        $bio = sanitize_textarea_field($bio);

        if ($userId < 1 || $displayName === '') {
            throw new RuntimeException('Enter the name you want shown around the Guild.');
        }

        if (! is_email($email)) {
            throw new RuntimeException('Enter a valid email address.');
        }

        $existing = email_exists($email);

        if ($existing && (int) $existing !== $userId) {
            throw new RuntimeException('That email address already belongs to another Guild member.');
        }

        $result = wp_update_user([
            'ID' => $userId,
            'display_name' => $displayName,
            'user_email' => $email,
        ]);

        if (is_wp_error($result)) {
            throw new RuntimeException(
                $result->get_error_message()
                ?: 'The Guild could not update those profile details.'
            );
        }

        update_user_meta(
            $userId,
            self::BIO_META,
            mb_substr($bio, 0, 500)
        );
    }
}
