# Phase III.12.6C — The Paladin's Sacred Reserves

III.12.6C turns the Paladin's read-only Sacred Register capacities into
persistent active-play resources.

## Paladin-owned reserves

The shared `ActiveClassResourceState` now tracks expenditure for:

- Lay on Hands
- Divine Sense
- Cleansing Touch

Permanent maximums remain owned by `PaladinSacredPolicy`.

Spell slots are deliberately not duplicated here. They remain owned by the
spellcasting architecture so Divine Smite and Paladin spells can later consume
the same slot state.

## Lay on Hands

Maximum remains:

`5 × Paladin level`

The reserve service supports spending more than one point in one validated
operation, which prepares the pool for variable healing amounts in III.12.6D.

The III.12.6C Ledger exposes a simple one-point spend control for reserve
verification only.

## Divine Sense

Divine Sense uses persist in the same active resource ledger and restore on a
long rest.

## Cleansing Touch

Cleansing Touch remains locked before Level 14.

At Level 14 it gains its certified reserve and can be spent.

## Long rest

A long rest restores all Paladin-owned Sacred Reserves without wiping unrelated
class-resource state.

## Level changes

Only expenditure is persisted.

That means Lay on Hands naturally reconciles against the new maximum when a
Paladin levels up; no stored maximum migration is required.

## Security

Sacred Reserve forms use the existing `gmrc_app_request` admin-post bridge and
the Paladin-specific `gmrc_character_sacred_{id}` nonce.

## Next slice

III.12.6D can now attach these reserves to actual Sacred Actions:

- variable Lay on Hands healing;
- Divine Sense use;
- Cleansing Touch use;
- Divine Smite through the shared spell-slot architecture.
