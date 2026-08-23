<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use RuntimeException;

defined('ABSPATH') || exit;

final class RegisterGuildMember
{
    public function handle(
        string $username,
        string $email,
        string $password,
        string $displayName,
        string $accountType
    ): int {
        $username = sanitize_user($username, true);
        $email = sanitize_email($email);
        $displayName = sanitize_text_field($displayName);

        $this->guard($username, $email, $password, $accountType);

        $userId = wp_insert_user([
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'display_name' => $displayName !== '' ? $displayName : $username,
            'role' => AccountType::role($accountType),
        ]);

        if (is_wp_error($userId)) {
            throw new RuntimeException(
                $userId->get_error_message()
                ?: 'The Guild could not create this account.'
            );
        }

        $user = get_userdata((int) $userId);
        if (! $user) {
            throw new RuntimeException('The Guild account was created but could not be reopened. Please contact the Steward.');
        }

        $role = AccountType::role($accountType);
        if (! in_array($role, (array) $user->roles, true)) {
            $user->set_role($role);
        }

        update_user_meta(
            (int) $userId,
            GuildProfile::ACCOUNT_TYPE_META,
            $accountType
        );

        return (int) $userId;
    }

    private function guard(
        string $username,
        string $email,
        string $password,
        string $accountType
    ): void {
        if ($username === '' || ! validate_username($username)) {
            throw new RuntimeException('Choose a valid Guild username.');
        }

        if (! is_email($email)) {
            throw new RuntimeException('Enter a valid email address.');
        }

        if (mb_strlen($password) < 10) {
            throw new RuntimeException('Your passphrase must be at least 10 characters.');
        }

        if (! in_array($accountType, AccountType::values(), true)) {
            throw new RuntimeException('Choose Player or Dungeon Master.');
        }

        if (username_exists($username)) {
            throw new RuntimeException('That Guild username is already registered.');
        }

        if (email_exists($email)) {
            throw new RuntimeException('That email address is already registered.');
        }
    }
}
