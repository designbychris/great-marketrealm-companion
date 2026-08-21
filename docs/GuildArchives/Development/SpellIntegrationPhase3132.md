# Phase III.13.2 — Spell Integration

## Guild Seal

**Status:** Compatibility integration implemented — awaiting server PHPUnit certification.

Phase III.13.2 begins connecting character-facing spell presentation to the
certified Sage's Spellbook register without rewriting persisted character
spell IDs.

## Stable IDs First

Existing `Spellbook` persistence remains unchanged.

Characters continue to know spells by their established internal ability IDs.
The integration layer changes only the player-facing reference presented above
those stable IDs.

## Canonical Compatibility Resolver

`CanonicalSpellReferenceResolver` bridges existing Arcane Pantry spell
definitions to the certified `HandbookSpellRegister`.

Only explicit, source-supported aliases are mapped in this first integration
slice:

- `restorative-preserve` → `Cure Meats`
- `market-missile` → `Mystery Mustard Missile`
- `aisle-lightning` → `Lightning Lemonade`
- `stockroom-fireball` → `Flame-Grilled Fireball`

Unmatched Arcane Pantry spells remain unchanged and are reported as
`unmatched`. The Companion does not guess their canonical identity.

## Character Surfaces

The Arcane Pantry now exposes:

- stable internal spell ID;
- canonical integration status;
- canonical Spellbook key when known;
- canonical display name when known;
- legacy display name;
- canonical source issues.

Wizard Spellbook and Cantrip advancement folios use the same resolver for
player-facing labels/details while preserving their existing selection keys.

## Preservation Boundary

This phase does **not**:

- rewrite stored Spellbook IDs;
- migrate character repository data;
- alter spell-slot progression;
- change active resource persistence;
- infer mappings for unmatched spells.

Further alias expansion should only happen when canonical equivalence can be
supported by the handbook or a deliberate project migration decision.
