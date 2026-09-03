# Phase IV.34.6A.3 — The Chronicle Knows Who Spoke

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit and browser certification.

Player memories now retain an author snapshot, optionally bind to the adventurer whose voice they represent, render beneath their certified Tabletop Session in the Company Chronicle, and project into the Dungeon Master Session Ledger without exposing private Keeper preparation to players. Sub-minute Session duration is omitted from the Fellowship Session masthead rather than displayed as `0m`.

The Fellowship Chronicle remains the shared source record. The Session Ledger stores a DM-facing projection keyed by the immutable Companion Session identifier.
