# Phase III.12.7C — The Warlock's Pact Reserves

III.12.7C turns Pact Magic from a read-only capacity into a persistent
active-play reserve.

## Pact Magic resource identity

Warlock Pact Magic uses one resource key:

`pact-magic-slot`

This is intentionally different from the shared standard spell-slot keys such
as `spell-slot-1`.

A Warlock's Pact slots are a single pool at one current slot level and all
refresh together.

## Persistent expenditure

Only expenditure is stored.

Permanent maximum and slot level continue to come from
`WarlockPatronPolicy`.

This means level changes reconcile naturally. A Warlock who has spent one of
two Pact slots at Level 10 has one remaining; after reaching Level 11 the same
one expenditure is measured against the new maximum of three, leaving two
remaining.

## Rest behaviour

Pact Magic restores on:

- short rest;
- long rest.

A Pact rest clears only `pact-magic-slot` expenditure and does not erase
unrelated active class-resource state.

## Arcane Pantry

Warlocks no longer use `SharedSpellSlotReserveService` for active slot
presentation.

Their Arcane Pantry slot row is supplied by `WarlockPactReserveService` so the
same persistent Pact reserve shown in the Patron Contract Register is also the
slot state shown in the general magic ledger.

## Ledger controls

The Patron Contract Register now provides:

- Spend Pact Slot
- Take Short Rest
- Take Long Rest

The header seal shows `remaining / maximum` alongside the current Pact slot
level.

## Security

Pact forms use the existing `gmrc_app_request` bridge with the dedicated nonce:

`gmrc_character_pact_{id}`

## Boundaries

III.12.7C does not yet:

- choose or replace Eldritch Invocations;
- choose a Pact Boon;
- cast a named Warlock spell;
- spend Mystic Arcanum;
- activate Patron-specific Gifts.

Those remain later Warlock slices.
