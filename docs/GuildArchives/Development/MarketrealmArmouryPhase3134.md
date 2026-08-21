# Phase III.13.4 — The Marketrealm Armoury

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.13.4 opens the mundane shelves of the Guild Library and connects
them to the existing Character Inventory without rewriting existing inventory
records.

## Provenance Is Explicit

The Armoury deliberately separates two kinds of record.

### Handbook-mentioned

These equipment names are explicitly present in class starting-equipment or
proficiency material in **The Great Marketrealm - Players Handbook**.

Examples include:

- dagger;
- shortsword;
- rapier;
- longsword;
- shortbow;
- scimitar;
- leather armour;
- shield;
- explorer's pack.

The Library does not claim that every mechanical field in the standard
equipment profile was separately stated in the handbook section currently
used for the import.

### Standard-compatible

The Companion also needs a broad practical mundane equipment range. These
records provide familiar fantasy weapons and armour without representing them
as Marketrealm-handbook canon.

This distinction is visible to players in the Guild Library.

## Character Inventory Integration

`ItemCatalogue` retains every existing Phase III.3 item ID and imports new
Armoury records after those legacy defaults.

Existing inventory storage remains untouched.

The current Guild Stores interface already consumes `ItemCatalogue::all()`,
so the expanded equipment becomes available through the existing Quartermaster
workflow without a second inventory system.

## Combat Integration

`ItemDefinition` gains an optional range field at the end of its constructor,
preserving all existing callers.

`AttackPresenter` now:

- keeps melee weapons on the established Strength / finesse logic;
- uses Dexterity for ranged weapons;
- presents the equipment record's reach/range rather than hard-coding every
  weapon as `Melee · 5 ft`.

## Mundane Boundary

III.13.4 contains no magical item, relic, artefact or enchanted-equipment
category.

Those remain reserved for:

**Phase III.13.5 — Relics of the Marketrealm**

## Certification Baseline

Incoming certified baseline:

- 3,319 tests;
- 10,793 assertions;
- all green.

Once the new regression suite and browser review pass, the Marketrealm
Armoury can receive its Guild certification.
