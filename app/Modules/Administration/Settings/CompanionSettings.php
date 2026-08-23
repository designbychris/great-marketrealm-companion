<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Settings;

defined('ABSPATH') || exit;

final class CompanionSettings
{
    public const OPTION_NAME = 'gmrc_companion_settings';

    /** @return array{steward_email:string,show_environment_details:bool} */
    public function all(): array
    {
        $stored = get_option(self::OPTION_NAME, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'steward_email' => (string) ($stored['steward_email'] ?? get_option('admin_email', '')),
            'show_environment_details' => ! array_key_exists('show_environment_details', $stored)
                || ! empty($stored['show_environment_details']),
        ];
    }

    public function save(string $stewardEmail, bool $showEnvironmentDetails): void
    {
        update_option(
            self::OPTION_NAME,
            [
                'steward_email' => sanitize_email($stewardEmail),
                'show_environment_details' => $showEnvironmentDetails,
            ],
            false
        );
    }
}
