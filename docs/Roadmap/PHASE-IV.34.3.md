# Phase IV.34.3 — The Fellowship Remembers

The Tabletop Session lifecycle now publishes a deliberately safe, shared record into the linked Fellowship's Company Chronicle when a Session ends.

## Boundary

- Tabletop remains authoritative for played-session timing.
- The Companion DM Session Ledger remains the private/canonical Keeper record.
- The Fellowship Company Chronicle receives only public play facts: Session number/title, Campaign name, played date, duration, and immutable integration IDs.
- Prep notes and DM recap text are never copied by this bridge.

## Idempotency

Chronicle entries retain the immutable Tabletop Session ID as source metadata. Re-synchronising or backfilling the same ended Session updates its existing certified Company Deed instead of creating a duplicate.

## Lifecycle

Active Sessions remain private to the DM Session Ledger. The shared Company Chronicle entry is written only once the Session has ended. Existing ended Sessions are also eligible during IV.34.2 link/backfill synchronisation.
