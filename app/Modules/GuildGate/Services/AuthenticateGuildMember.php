<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use RuntimeException;
use WP_User;

defined('ABSPATH') || exit;

final class AuthenticateGuildMember
{
    public function handle(
        string $login,
        string $password,
        bool $remember
    ): WP_User {
        if (trim($login) === '' || $password === '') {
            throw new RuntimeException(
                'Enter your Guild username or email and passphrase.'
            );
        }

        $user = wp_signon([
            'user_login' => sanitize_text_field($login),
            'user_password' => $password,
            'remember' => $remember,
        ], is_ssl());

        if (is_wp_error($user)) {
            throw new RuntimeException(
                'Those Guild credentials could not be verified.'
            );
        }

        return $user;
    }
}
