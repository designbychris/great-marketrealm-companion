Phase III.M.3C — Adventurer's Vitality (initial implementation)
Apply to the current deployed GMRC plugin, preserving paths.
POST /wp-json/gmrc-pocket/v1/characters/{ULID}/vitality accepts JSON integer fields current, temporary, expected_current, expected_temporary.
Uses WordPress REST cookie authentication + X-WP-Nonce, owner-scoped repository find, and existing Character::updateVitalMeasures/save.
Stale expected HP returns 409. Maximum HP is never writable. Temporary HP range 0–999.
IMPORTANT: PHP and JavaScript syntax checks only; no full PHPUnit run or live write test performed. Test on staging before production. Dedicated endpoint integration tests are still required.
