# Phase III.15 — The Dungeon Master's Desk

## Certified incoming baseline

- 3,385 tests
- 11,148 assertions
- all green

## III.15 — Desk Foundation

The Dungeon Master's Desk establishes the DM-only workspace that will contain the later campaign-management programme.

The Desk is implemented as its own Companion Kingdom and module rather than as a special case inside Dashboard or Guild Gate. Its route is `dungeon-master`, and its navigation contribution is visible only to members who hold the `gmrc_manage_campaigns` capability or WordPress administrative access.

The controller independently enforces the same permission boundary. A Player who manually requests the route receives an HTTP 403 and a themed sealed-desk notice rather than access to DM content. This makes navigation visibility a convenience while capability checks remain the actual security boundary.

WordPress administrators continue to receive both Player-facing Companion access and DM-facing access through the Guild Gate role contract.

## Initial Desk Surface

The first Desk provides:

- a private DM workspace hero and status panel;
- the future Campaign Register as the next ledger to open;
- placeholders for Session Ledger, Encounter Board, and Player Roster;
- quick links to the existing Character Register, Fellowships, and Guild Library;
- responsive, keyboard-focus, reduced-transparency, no-backdrop-filter, and forced-colour support.

## Next certified slice

**Phase III.15.1 — The Campaign Register** should establish campaign records, ownership, membership invitations/assignments, and the relationship between campaigns, Fellowships, characters, and Guild accounts.
