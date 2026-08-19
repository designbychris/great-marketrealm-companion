# Phase III.12.8A — The Sorcerer's Origin Spark Register

III.12.8A adds the Sorcerer's read-only Origin Spark Register.

## Register contents

The Spells & Abilities Ledger now exposes:

- chosen Sorcerous Origin;
- Sorcery Point maximum;
- Metamagic options known;
- spells known;
- cantrips known;
- highest available spell circle;
- current shared spell-slot state;
- Charisma-based spell save DC;
- Charisma-based spell attack;
- next major Sorcerer milestone.

## Repository source boundary

The current Grand Catalogue contains five Sorcerous Origins:

- Juiced-Blooded
- Sugarspark Soul
- Carbonation Soul
- Soda-Born
- Dairyblooded Soul

Their current source descriptions are intentionally minimal and only identify
each entry as a Sorcerer path.

III.12.8A does not invent identity, playstyle or best-for guidance that is not
present in the repository.

Those richer descriptions can be added later when the corresponding source
material is available.

## Read-only boundary

This slice does not yet:

- spend Sorcery Points;
- convert Sorcery Points to spell slots;
- convert spell slots to Sorcery Points;
- select or use Metamagic;
- define Origin Gifts.

Those remain later III.12.8 slices.

## Shared spell slots

Sorcerer is a conventional full caster, so the Register reads the same
persistent standard spell-slot reserve used by the Arcane Pantry rather than
introducing a Sorcerer-specific slot identity.

## Accessibility

The Register has an explicit heading relationship, responsive stacking and
forced-colours support.
