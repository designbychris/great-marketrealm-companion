# Phase III.15 — The Dungeon Master's Desk

## Certified incoming baseline

- 3,397 tests
- 11,204 assertions
- all green

## III.15 — Desk Foundation

The Dungeon Master's Desk is the capability-protected workspace for the DM programme. Navigation capability checks happen at request time, never during Kingdom boot, while controllers remain the server-side security boundary.

## III.15.1 — The Campaign Register

The Campaign Register establishes campaigns as first-class, DM-owned Companion records.

The first certified slice provides:

- private `gmrc_campaign` WordPress persistence;
- ULID campaign identity;
- Dungeon Master ownership through the WordPress post author;
- ownership-scoped repository reads and writes;
- Create, View, Edit, and Archive workflows;
- active and archived campaign states without destructive deletion;
- dedicated command nonces through the existing Companion `admin-post` pipeline;
- `gmrc_manage_campaigns` authorization on campaign form requests;
- an open Campaign Register link from the Dungeon Master's Desk;
- responsive, keyboard-focus, reduced-transparency, and forced-colour treatment.

Campaign membership, Player invitations, Character/Fellowship assignment, Sessions, and Encounters deliberately remain outside this slice. They will attach to the stable campaign identity established here.

## Next certified slice

**Phase III.15.2 — The Player Roster** should begin connecting Guild accounts and their Characters to DM-owned campaigns without weakening campaign ownership boundaries.

## III.15.1A — Dungeon Master's Desk Visual Treatment

Certified incoming baseline: **3,403 tests / 11,216 assertions — all green**.

The Dungeon Master's workspace now uses the dedicated
`assets/images/dungeon-master/dungeon-master-desk-background.png` artwork across
both the Desk and Campaign Register. The image is scoped only to the direct
`.gmrc-content` workspace with `:has(> ...)`, keeping the global application
navigation outside the illustrated layer.

The treatment includes a transparent artwork-first hero, dark translucent
command panels, gold-edged DM ledger cards, integrated Campaign Register
surfaces, mobile-safe background positioning, keyboard focus treatment,
reduced-transparency fallbacks, no-backdrop-filter fallbacks, and forced-colour
support.

## III.15.2 — The Player Roster

### Certified incoming baseline for III.15.2

- 3,409 tests
- 11,246 assertions
- all green

The Player Roster connects existing Player Guild accounts to DM-owned campaigns while preserving campaign ownership and character authorship boundaries.

The first slice provides:

- exact username/email lookup for existing Companion Player accounts;
- campaign-scoped Player membership stored on the private campaign record;
- profile portrait, display identity, and optional Guild bio presentation;
- owner-aware Character reads for rostered Players;
- explicit attach/detach actions for a Player's own Characters;
- server-side verification that attached Characters belong to the rostered Player;
- remove-Player behaviour that also clears the campaign's character links for that Player;
- campaign-scoped nonces and `gmrc_manage_campaigns` authorization;
- immersive Dungeon Master's Desk visual treatment with responsive and accessibility fallbacks.

Co-DM permissions, email invitations, pending invitations, Sessions, and Encounters remain outside this slice.

## Next certified slice

**Phase III.15.3 — The Session Ledger** should establish campaign-owned session records and attendance against the stable Campaign Roster.

## III.15.3 — The Session Ledger

### Certified incoming baseline for III.15.3

- 3,417 tests
- 11,301 assertions
- all green

The Session Ledger establishes Sessions as first-class, campaign-owned Dungeon Master records.

This slice provides:

- private `gmrc_session` WordPress persistence with permanent ULID identity;
- stable Campaign ID and WordPress parent linkage back to the owned Campaign record;
- DM-owned, campaign-scoped Session queries and writes;
- Session number, title, optional date, Planned / Played / Cancelled status;
- Dungeon Master preparation notes and post-session recap;
- attendance selected only from the stable Campaign Player Roster;
- Character attendance limited to Characters already attached to the attending Player;
- Create, View, Edit, and Ledger-index workflows;
- campaign-scoped Session nonces and `gmrc_manage_campaigns` authorization;
- archived Campaigns preserving a read-only Session history;
- immersive Dungeon Master workspace styling with responsive and accessibility fallbacks.

Encounter planning remains outside this slice. The Session identity established here is intended to become the parent context for Encounter Board records.

## Next certified slice

**Phase III.15.4 — The Encounter Board** should create campaign/session-scoped encounters without weakening the Campaign, Player Roster, or Session ownership boundaries.
