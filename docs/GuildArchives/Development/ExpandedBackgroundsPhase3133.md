# Phase III.13.3 — Expanded Backgrounds

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.13.3 registers and integrates the five optional Marketrealm
backgrounds supplied by The Great Marketrealm - Players Handbook.

## Canonical Background Register

The handbook supplies exactly five optional backgrounds:

- Crateborn Noble
- Backshelf Forager
- Discount Bin Survivor
- Cleaner’s Acolyte
- Cart Ranger

Each source entry supplies:

- one named Feature and its effect;
- exactly two Skills;
- exactly one Tool proficiency.

The source does not state languages, equipment or starting wealth. Those
fields are not inferred from ordinary D&D backgrounds.

## Character Generator Integration

The five canonical Marketrealm backgrounds are now valid immutable
`Background` values and appear automatically in create/edit selectors.

Their skill and tool proficiencies are applied by the existing Character
registration pipeline.

Their language-choice count is zero because the source grants no languages.

The generator and edit preview also display the handbook Feature name and
effect from the canonical Background Register.

## Tool Register Expansion

Three tool proficiencies required by the handbook are now first-class
`ToolProficiency` values:

- Cartographer's Tools
- Alchemist's Supplies
- Navigator's Tools

Existing tool proficiencies remain unchanged.

## Existing Characters

The previous eight supported Background identifiers remain valid and in their
existing order relative to one another. No persisted Background ID is renamed
or migrated.

## Guild Library

The Background Reference Catalogue is now `registered` with five canonical
entries and `/library/backgrounds` opens the read-only Background Register.

## Preservation Boundary

This phase does not invent missing handbook languages, equipment or wealth,
rename existing backgrounds, migrate stored character IDs, or alter Calling,
spell, advancement, portrait, purse or Fellowship mechanics.
