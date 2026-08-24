# Phase III.16.14 — Guild Session & Account Security Certification

Phase III.16.14 certifies the boundary between ordinary WordPress authentication and actual Great Marketrealm Companion membership.

## Canonical Guild admission

A valid WordPress login is no longer sufficient to enter the Companion. A signed-in account must also hold the canonical `gmrc_access_companion` capability assigned to registered Marketrealm Players, Dungeon Masters, and WordPress administrators.

`GuildAccessPolicy` is the shared admission service used by the front-end application shell and command gateway. This prevents unrelated WordPress accounts from entering Companion routes merely because they possess a valid site session.

## Login certification

`AuthenticateGuildMember` still delegates credential verification to WordPress. After successful authentication it now certifies the returned user against the Guild access policy. If that account is not enrolled for Companion access, the newly established authentication cookie is cleared, the current user is reset, and entry is refused with a readable Guild Gate error.

Registered Player, Dungeon Master, and administrator accounts retain their established access capability and continue through the existing login path unchanged.

## Existing-session certification

If a WordPress account is already signed in but no longer holds Companion access, the Companion does not silently log the account out and does not render the application shell. It presents a dedicated **Guild papers required** admission certificate explaining that the account is signed in to WordPress but is not currently enrolled in the Companion.

The refusal surface offers WordPress's nonce-protected logout URL so the visitor may safely sign out and return to the Guild Gate with another account.

This also means capability removal takes effect on the next Companion request even if an older WordPress session remains active.

## Command gateway boundary

The established Phase III.16 application gateway remains in place. Anonymous login and registration are still explicitly public. Every other Companion command now requires `GuildAccessPolicy::allowsCurrentUser()` before nonce resolution or route dispatch.

No competing `admin-post.php` action family is introduced.

## Asset boundary

Anonymous visitors and signed-in accounts without Guild admission receive only the Guild Gate treatment. Full Companion component, script, theme, Guild Profile, and Dungeon Master Desk assets are reserved for certified Guild sessions.

## Certified incoming baseline

- 3,648 tests
- 13,211 assertions
- ALL GREEN
