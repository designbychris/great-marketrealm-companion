# Phase III.12.8 — The Sorcerer's Calling

III.12.8 promotes Sorcerer from the generic registered-Calling fallback into a
specialist Calling.

## Repository-backed Sorcerer material

The repository already defines five Marketrealm Sorcerous Origins:

- Juiced-Blooded
- Sugarspark Soul
- Carbonation Soul
- Soda-Born
- Dairyblooded Soul

It also already exposes Sorcerer-compatible Arcane Pantry abilities including:

- Produce Spark
- Market Missile
- Crate Bolt
- Aisle Step
- Cold Aisle Shard
- Aisle Lightning
- Stockroom Fireball

III.12.8 preserves those existing identities.

## Level-one foundations

Sorcerer now has two explicit Calling foundations:

- Spellcasting
- Sorcerous Origin

The Origin is registered with the shared `PathProgressionCatalogue` as a Level
1 choice using the `Origin Spark Folio`.

## Calling-owned progression

The specialist Calling spine establishes:

- Level 2 — Font of Magic
- Level 3 — Metamagic, two options known
- Level 10 — third Metamagic option
- Level 17 — fourth Metamagic option
- Level 20 — Sorcerous Restoration

Font of Magic records the permanent rule that the Sorcery Point maximum equals
Sorcerer level. Persistent expenditure belongs to a later Sorcerer reserve
slice rather than being stored as advancement data.

The shared Measure of Growth remains delegated at Levels 4, 8, 12, 16 and 19.

Later Origin Gifts are reserved for Levels 6, 14 and 18. The defining Origin
identity begins at Level 1.

## Spellcasting model

Unlike Warlock Pact Magic, Sorcerer is a conventional full caster and is now
registered with `SpellcastingProgressionCatalogue`.

Its progression model is `known-spells`, not Wizard `spellbook`.

The reference records known-spell and cantrip totals through Level 20, along
with the highest available spell circle and the number of newly learned spells
or cantrips at each advancement.

## Phase boundaries

III.12.8 does not yet implement:

- Origin Gift definitions;
- persistent Sorcery Point expenditure;
- Font of Magic conversion actions;
- Metamagic option selection;
- Metamagic expenditure;
- active Sorcerer-specific casting controls.

Those are reserved for the later III.12.8 slices.
