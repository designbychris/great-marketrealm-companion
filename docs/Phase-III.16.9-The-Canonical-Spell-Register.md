# Phase III.16.9 — The Canonical Spell Register

## Certified incoming baseline

- 3,568 tests
- 12,414 assertions
- all green

## Purpose

The Canonical Spell Register gives the Steward a protected editing surface over the existing Players Handbook spell transcription without creating a second spell database.

The bundled `HandbookSpellRegister` remains the immutable source baseline. `CanonicalSpellRegister` applies a separate `gmrc_canonical_spell_overrides` option overlay for Steward-maintained Marketrealm spell names, retained source-variant wording, and private notes.

## Safety boundary

Stable spell identity remains read-only in this slice: canonical key, kind, original spell, level, school, class/access labels, and source-variant identity are not editable. Existing Character spell persistence and slot mechanics are not rewritten.

Sage's Spellbook, the Guild Library spell catalogue, and the Character canonical spell-reference resolver now read through the shared canonical overlay. This allows Steward wording changes to propagate consistently while stable Character spell IDs remain unchanged.

## Steward commands

Spell writes require `manage_options` plus record-specific nonces. Each overridden record can be restored to the Players Handbook baseline without modifying the bundled source transcription.
