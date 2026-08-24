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


## Phase III.16.11 — Market Pass Foundation

Market Passes establish the secure Dungeon Master-to-Player campaign invitation layer.

- Every active Campaign can issue one current, cryptographically random human-friendly Market Pass.
- Codes use an ambiguity-resistant alphabet, are case-insensitive, and expire after seven days.
- Dungeon Masters can rotate or revoke a pass without removing Players who already joined.
- A Player redeems a pass against their authenticated Guild account; no username or email needs to be exchanged.
- Redemption adds account-level Campaign membership only. Character nomination remains a later Active Campaigns/roster phase.
- Archived Campaigns, expired/revoked codes, self-invites, duplicate membership, and code collisions are explicitly guarded.
- The underlying `InviteCodeGenerator` lives in Core so Fellowship Seals can reuse the same invitation primitive later.

### Active Campaigns

Player Campaign membership now has a dedicated, player-safe Active Campaigns surface. It is resolved from Campaign roster membership established by Market Passes and deliberately excludes Dungeon Master sessions, encounters, journals and command-centre controls. Character assignment remains a separate concern for the following Campaign roster phase.

### Character Assignment, DM Roster & Campaign Fellowships

Active Campaign membership now bridges into the adventuring company without collapsing account membership and Character identity into one record.

- Players nominate exactly one Character they own for each joined active Campaign, and may change or clear that nomination while the Campaign remains active.
- Character ownership is re-checked server-side; a Player cannot nominate another Guild account's adventurer.
- The Dungeon Master roster immediately reflects the same roster assignment data.
- A Dungeon Master may **Found a Fellowship from the roster**, snapshotting the currently nominated adventurers into a new Campaign Fellowship, or link an existing Fellowship from their Fellowship Register.
- Campaign-to-Fellowship linkage is stored separately from the Fellowship aggregate. Releasing a link never deletes the Fellowship.
- Founding is deliberately a one-time snapshot: later Campaign roster changes do not silently add or remove Fellowship members. Fellowship Seals will own the future account-to-account membership workflow.
- Campaign-founded Fellowships may resolve explicitly nominated Character references across Guild accounts for presentation, while normal Character ownership mutations remain owner-scoped.
- Market Pass remains the primary Player invitation route; direct username/email roster entry is retained only as a Dungeon Master fallback.

### Shared Campaign Fellowship Membership

Campaign-founded Fellowships remain owned by their Dungeon Master custodian, while Guild accounts whose nominated Characters were captured into the Fellowship can now discover and open that Fellowship from their own Fellowship Register. Membership never transfers administrative ownership. Shared members may move coin between the Fellowship Treasury and their own member Character purse; direct company adjustments, roster administration and permanent Fellowship editing remain custodian-only. Treasury movements continue to use the existing recorded transfer ledger.
