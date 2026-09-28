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
    public const CONTRACT_VERSION = '1.0';

    /** @return array<string,mixed> */
    public static function contract(): array
    {
        return [
            'contract_version' => self::CONTRACT_VERSION,
            'product' => 'great-marketrealm-pocket-companion',
            'transport' => [
                'origin' => home_url('/'),
                'rest_root' => rest_url('gmrc-pocket/v1/'),
                'authentication' => 'wordpress-cookie-rest-nonce',
                'same_origin_required' => true,
                'offline_character_writes' => false,
            ],
            'endpoints' => [
                'bridge' => rest_url('gmrc-pocket/v1/bridge'),
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
            ],
        ];
    }
}
