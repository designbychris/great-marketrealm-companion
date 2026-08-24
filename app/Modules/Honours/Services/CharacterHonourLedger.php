<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

/**
 * Append-only Character distinctions stored beneath the owning Guild account.
 */
final class CharacterHonourLedger
{
    private const META_KEY = '_gmrc_character_honours';

    /** @return array<string,string> */
    public function allForCharacter(int $accountId, CharacterId $characterId): array
    {
        if ($accountId <= 0) {
            return [];
        }

        $stored = get_user_meta($accountId, self::META_KEY, true);
        if (! is_array($stored)) {
            return [];
        }

        $characterHonours = $stored[$characterId->value()] ?? [];
        if (! is_array($characterHonours)) {
            return [];
        }

        $honours = [];
        foreach ($characterHonours as $key => $certifiedAt) {
            $key = sanitize_key((string) $key);
            if ($key === '') {
                continue;
            }

            $honours[$key] = sanitize_text_field((string) $certifiedAt);
        }

        return $honours;
    }

    /**
     * @param array<int,string> $keys
     * @return array<string,string>
     */
    public function certify(int $accountId, CharacterId $characterId, array $keys): array
    {
        $stored = get_user_meta($accountId, self::META_KEY, true);
        $stored = is_array($stored) ? $stored : [];
        $honours = $this->allForCharacter($accountId, $characterId);
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
            $stored[$characterId->value()] = $honours;
            update_user_meta($accountId, self::META_KEY, $stored);
        }

        return $honours;
    }
}
