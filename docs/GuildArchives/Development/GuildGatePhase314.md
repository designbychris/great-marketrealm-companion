# Phase III.14 — The Guild Gate

## Guild Seal

**Status:** Foundation implemented — awaiting server PHPUnit certification.

Certified baseline entering this phase:

- 3,354 tests
- 11,003 assertions
- all green

## Purpose

The Guild Gate establishes Companion-owned authentication and account roles
before the later Dungeon Master programme begins.

A visitor who reaches the Companion while signed out no longer receives the
signed-in application shell. Instead, the shortcode renders a dedicated
Guild Gate with secure sign-in and registration forms.

## Guild Accounts

Registration creates a real WordPress user and requires the member to choose
one of two Companion account types:

- **Player** → `gmrc_player`
- **Dungeon Master** → `gmrc_dm`

The DM role is deliberately not a WordPress editorial or administrative role.
It receives the Companion capability `gmrc_manage_campaigns`, which is the
future server-side permission boundary for DM tools.

Both Companion roles receive `gmrc_access_companion` and WordPress `read`.
Administrators receive the two Companion capabilities so site stewardship is
not locked out of later DM tooling.

## Profile Contract

The first account metadata contract reserves:

- `gmrc_account_type`
- `gmrc_profile_portrait_attachment_id`

The Guild Navigation already consumes the portrait attachment when present
and falls back to the normal WordPress avatar when it is absent. The upload
and profile-editing workflow remains a later Guild Gate slice.

## Security Boundary

The existing `admin-post.php` application command bridge remains the only
form command entrypoint.

Guild Gate login and registration each have dedicated nonce actions. While a
visitor is signed out, every other Companion command is rejected before route
dispatch. The registration service maps the validated Player/DM choice to a
fixed role and never accepts an arbitrary WordPress role from submitted input.

Passwords are handed directly to WordPress authentication/user APIs and are
never stored in Companion metadata.

## Return Journey

The Gate preserves a valid internal `gmrc_route` while authentication takes
place. A member who originally requested a Library, Character or Fellowship
route can therefore return to that route after a successful login or signup.

Logout now returns to the Companion URL so the Guild Gate is shown again.

## Presentation & Accessibility

The Gate is a responsive, parchment-on-dark-wood welcome screen with:

- semantic labels and fieldsets;
- browser password-manager autocomplete semantics;
- visible keyboard focus states;
- a WordPress password-recovery path;
- forced-colour support;
- reduced-transparency support;
- a no-`backdrop-filter` fallback.

## Next Guild Gate Slices

The account foundation intentionally leaves these for dedicated certified
work:

1. Companion profile editing and portrait upload/remove controls.
2. Account preferences and any additional user metadata required by play.
3. DM-specific navigation and campaign-management surfaces guarded by
   `gmrc_manage_campaigns`.
4. Ownership/audit rules that connect Characters, Fellowships and later
   Campaigns to Guild accounts.
