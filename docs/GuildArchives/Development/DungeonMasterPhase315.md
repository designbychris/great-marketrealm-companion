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

## III.15.4 — The Encounter Board

### Certified incoming baseline for III.15.4

- 3,426 tests
- 11,364 assertions
- all green

The Encounter Board establishes encounters as first-class, campaign-owned Dungeon Master records that may be prepared independently and assigned to a Session when the story is ready.

This slice provides:

- private `gmrc_encounter` WordPress persistence with permanent ULID identity;
- stable Campaign ownership and WordPress parent linkage;
- optional assignment to a Session that must belong to the same owned Campaign;
- Prepared / Running / Completed encounter lifecycle;
- Low / Moderate / High / Deadly threat designation;
- location/environment, adversary roster, and Dungeon Master notes;
- participating Characters limited to Characters already attached through the Campaign Player Roster;
- Create, View, Edit, and Board-index workflows;
- campaign-scoped Encounter nonces and `gmrc_manage_campaigns` authorization;
- archived Campaigns preserving a read-only Encounter Board;
- immersive Dungeon Master workspace styling with responsive and accessibility fallbacks.

Initiative order, live combat state, hit-point tracking for adversaries, monster stat blocks, and completed combat history deliberately remain outside this first Encounter Board slice.

## Next certified slice

**Phase III.15.5 — The Initiative Table** should turn a prepared Encounter into a live table-facing combat workspace while preserving the Campaign, Session, Roster, Character, and Encounter ownership boundaries established so far.

## III.15.5 — The Initiative Table

### Certified incoming baseline for III.15.5

- 3,435 tests
- 11,427 assertions
- all green

The Initiative Table turns a prepared Encounter into a persistent live-play workspace without creating a second encounter model.

This slice provides:

- Encounter-owned persistent initiative state stored on the private Encounter record;
- combatants seeded only from the Encounter's participating Campaign Characters and adversary roster;
- Player Character initiative modifiers and certified maximum HP drawn from the Character Ledger;
- secure d20 initiative rolling in the browser using the same `window.crypto.getRandomValues` principle as Guild Diceworks;
- manual initiative, current HP, adversary maximum HP, conditions, and down-state tracking;
- deterministic initiative sorting, active-turn tracking, round advancement, and reset-from-Encounter behaviour;
- completion of the Initiative Table promoting the Encounter to Completed;
- dedicated Encounter-scoped Initiative nonces and `gmrc_manage_campaigns` authorization;
- archived Campaigns preserving the Initiative Table as read-only combat history;
- immersive Dungeon Master workspace styling with responsive, reduced-transparency, and forced-colour fallbacks.

The Initiative Table deliberately does not write DM-side HP changes back into a Player's Character Ledger. It is encounter-local combat state, preventing a Dungeon Master from silently mutating Player-owned character records.

## Next certified slice

**Phase III.15.6** should build on the live Encounter foundation without weakening the ownership boundaries certified through the Initiative Table.
