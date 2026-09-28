<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

defined('ABSPATH') || exit;

/**
 * Phase III.M.5C: stable contract advertised to future Pocket native shells.
 *
 * This is deliberately transport metadata, not a second character model. Native
 * clients must continue to use the owner-scoped Pocket REST endpoints below.
 */
final class PocketNativeBridge
{
    public const CONTRACT_VERSION = '1.1';

    /** @return array<string,mixed> */
    public static function contract(): array
    {
        return [
            'contract_version' => self::CONTRACT_VERSION,
            'product' => 'great-marketrealm-pocket-companion',
            'transport' => [
                'origin' => home_url('/'),
                'rest_root' => rest_url('gmrc-pocket/v1/'),
                'authentication' => [
                    'browser' => 'wordpress-cookie-rest-nonce',
                    'native' => 'system-browser-pkce-bearer',
                ],
                'same_origin_required' => false,
                'offline_character_writes' => false,
            ],
            'endpoints' => [
                'bridge' => rest_url('gmrc-pocket/v1/bridge'),
                'native_begin' => rest_url('gmrc-pocket/v1/native/begin'),
                'native_token' => rest_url('gmrc-pocket/v1/native/token'),
                'native_revoke' => rest_url('gmrc-pocket/v1/native/revoke'),
                'session' => rest_url('gmrc-pocket/v1/session'),
                'characters' => rest_url('gmrc-pocket/v1/characters'),
                'vitality' => rest_url('gmrc-pocket/v1/characters/{id}/vitality'),
                'spell_slots' => rest_url('gmrc-pocket/v1/characters/{id}/spell-slots'),
            ],
            'lifecycle' => [
                'resume' => 'revalidate-session',
                'connection_restored' => 'refresh-live-ledger',
                'cold_offline_launch' => 'public-connection-guard',
            ],
            'privacy' => [
                'character_cache' => 'none',
                'rest_cache' => 'none',
                'credentials_in_payload' => false,
                'wordpress_password_in_native_app' => false,
                'native_token_storage' => 'platform-secure-storage-required',
            ],
        ];
    }
}
