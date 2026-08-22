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
