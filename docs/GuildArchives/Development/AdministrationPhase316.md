# Phase III.16 — Companion Administration & Security

## Certified incoming baseline

- 3,490 tests
- 11,757 assertions
- all green

## Phase III.16.1 — The Steward's Office / Administration Foundation

The Steward's Office establishes the administrator-only WordPress workspace for the Companion's post-DM administration programme.

This foundation provides:

- a dedicated **Steward's Office** top-level WordPress administration screen;
- a strict `manage_options` capability boundary at both menu registration and render time;
- admin assets loaded only on the Steward's Office screen;
- foundation cards for Gate Security, Canonical Records, and Companion Settings;
- no editable credentials or canonical content in the foundation slice;
- a regression seal protecting the administration boundary; and
- a post-DM visual fix that defines the standard Dungeon Master's Desk background for the Campaign Command Centre.

Later III.16 slices can add anti-bot credentials, security policy, Bestiary/content stewardship, and other administrator controls without weakening this foundation.

## Phase III.16.4 — Canonical Records / Bestiary Stewardship

Canonical Records is now an active Steward workspace. The Dungeon Master Guide register remains the baseline while `gmrc_canonical_bestiary_overrides` stores administrator-owned tuning and WordPress Media Library attachment IDs. Canonical Bestiary cards link to full creature folios, and existing Encounter snapshots remain immutable historical combat preparation.
