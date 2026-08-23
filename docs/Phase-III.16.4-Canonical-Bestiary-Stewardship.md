# Phase III.16.4 — Canonical Records / Bestiary Stewardship

## Certified incoming baseline
- 3,513 tests
- 11,872 assertions
- all green

## Purpose
The Steward's Office now owns a controlled canonical-content layer for the Marketrealm Bestiary. The Dungeon Master Guide data file remains the immutable source baseline, while administrator changes are stored as separate canonical overrides.

## Steward workflow
- open **Canonical Records** from the Steward's Office;
- browse and search every canonical creature;
- edit name, type, size, alignment, AC, HP, speed, CR, ability scores, traits, actions, and Steward/lore notes;
- attach, replace, or remove creature artwork through the standard WordPress Media Library;
- restore an individual creature to its Dungeon Master Guide baseline at any time;
- protect every mutation with `manage_options` and a creature-specific nonce.

## Bestiary integration
Canonical cards in the DM Bestiary now open an interactive field folio. Assigned artwork appears on the Bestiary card and full folio with a torn-paper presentation. The full folio exposes combat statistics, ability scores, traits, actions, lore notes, and preserved canonical source warnings.

## Historical safety
Encounter snapshots remain historical snapshots. Steward changes affect future canonical reads and future Encounter preparation, but do not rewrite monster groups already stored inside an Encounter.

## Media rule
The canonical record stores the WordPress attachment ID, never a pasted file URL. The Steward picker only accepts image attachments so replacing Media Library derivatives does not require changing Bestiary code.
