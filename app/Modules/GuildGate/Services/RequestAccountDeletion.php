<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use RuntimeException;
use WP_User;

use function get_userdata;
use function get_user_meta;
use function sanitize_text_field;
use function update_user_meta;
use function wp_check_password;
use function wp_mail;

/**
 * Phase III.M.7C.5A — The Guild Knows When to Say Farewell.
 *
 * Records a verified erasure request rather than blindly deleting WordPress
 * rows. A Guild account may participate in shared Campaign and Fellowship
 * history, so the Registrar must detach/anonymise shared records before the
 * final WordPress account is erased.
 */
final class RequestAccountDeletion
{
    public const REQUESTED_AT_META = 'gmrc_account_deletion_requested_at';
    public const CONFIRMATION_PHRASE = 'DELETE MY ACCOUNT';

    public function handle(int $userId, string $password, string $confirmationPhrase): void
    {
        $user = get_userdata($userId);
        if (! $user instanceof WP_User) {
            throw new RuntimeException('The Guild account could not be found.');
        }

        if (! wp_check_password($password, (string) $user->user_pass, $userId)) {
            throw new RuntimeException('Your current password could not be verified.');
        }

        if (trim($confirmationPhrase) !== self::CONFIRMATION_PHRASE) {
            throw new RuntimeException('Type DELETE MY ACCOUNT exactly to confirm the request.');
        }

        if ($this->requestedAt($userId) !== '') {
            throw new RuntimeException('An account deletion request is already recorded for this Guild account.');
        }

        $requestedAt = gmdate('c');
        update_user_meta($userId, self::REQUESTED_AT_META, $requestedAt);

        $siteName = sanitize_text_field((string) get_bloginfo('name'));
        $adminEmail = sanitize_email((string) get_option('admin_email'));
        $subject = sprintf('[%s] Guild account deletion request', $siteName !== '' ? $siteName : 'Great MarketRealm');
        $message = implode("\n", [
            'A verified Great MarketRealm Guild account deletion request has been submitted.',
            '',
            'User ID: ' . $userId,
            'Username: ' . (string) $user->user_login,
            'Email: ' . (string) $user->user_email,
            'Requested at (UTC): ' . $requestedAt,
            '',
            'Before deleting the WordPress account, remove owned personal Companion data and detach or anonymise shared Campaign/Fellowship relationships so other members\' records remain intact.',
        ]);

        if ($adminEmail !== '') {
            wp_mail($adminEmail, $subject, $message);
        }

        if ((string) $user->user_email !== '') {
            wp_mail(
                (string) $user->user_email,
                'Your Great MarketRealm account deletion request',
                "Your request to delete your Great MarketRealm Guild account and associated personal data has been received.\n\nThe Registrar will process the request while preserving only shared or legally required records that cannot safely be removed.\n\nIf you did not make this request, contact Great MarketRealm support immediately."
            );
        }
    }

    public function requestedAt(int $userId): string
    {
        return trim((string) get_user_meta($userId, self::REQUESTED_AT_META, true));
    }
}
