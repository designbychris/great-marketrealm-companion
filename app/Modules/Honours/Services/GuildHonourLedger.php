<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

defined('ABSPATH') || exit;

/**
 * Persistent account-level record of honours already certified by the Guild.
 */
final class GuildHonourLedger
{
    private const META_KEY = '_gmrc_guild_honours';

    /** @return array<string,string> */
    public function allForAccount(int $accountId): array
    {
        if ($accountId <= 0) {
            return [];
        }

        $stored = get_user_meta($accountId, self::META_KEY, true);

        if (! is_array($stored)) {
            return [];
        }

        $honours = [];
        foreach ($stored as $key => $certifiedAt) {
            $key = sanitize_key((string) $key);
            if ($key === '') {
                continue;
            }

            $honours[$key] = sanitize_text_field((string) $certifiedAt);
        }

        return $honours;
    }

    /** @param array<int,string> $keys
     *  @return array<string,string>
     */
    public function certify(int $accountId, array $keys): array
    {
        $honours = $this->allForAccount($accountId);
        $changed = false;

        foreach ($keys as $key) {
            $key = sanitize_key($key);
            if ($key === '' || isset($honours[$key])) {
                continue;
            }

            $honours[$key] = gmdate('c');
            $changed = true;
        }

        if ($changed) {
            update_user_meta($accountId, self::META_KEY, $honours);
        }

        return $honours;
    }
}
