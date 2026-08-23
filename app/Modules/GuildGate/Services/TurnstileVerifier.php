<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use RuntimeException;

defined('ABSPATH') || exit;

final class TurnstileVerifier
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(private GateSecuritySettings $settings)
    {
    }

    public function verify(string $token, string $remoteIp = ''): void
    {
        if (trim($token) === '') {
            throw new RuntimeException('Please complete the Guild Gate security check.');
        }

        $configuration = $this->settings->all();
        $body = [
            'secret' => $configuration['secret_key'],
            'response' => trim($token),
        ];
        if ($remoteIp !== '') {
            $body['remoteip'] = $remoteIp;
        }

        $response = wp_remote_post(self::VERIFY_URL, [
            'timeout' => 8,
            'body' => $body,
        ]);

        if (is_wp_error($response)) {
            throw new RuntimeException('The Guild Gate security service could not be reached. Please try again.');
        }

        $decoded = json_decode((string) wp_remote_retrieve_body($response), true);
        if (! is_array($decoded) || empty($decoded['success'])) {
            throw new RuntimeException('The Guild Gate security check was not accepted. Please try again.');
        }
    }
}
