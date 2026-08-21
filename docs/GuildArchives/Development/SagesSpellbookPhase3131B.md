# Phase III.13.1B — Sage's Spellbook

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.13.1B turns the certified III.13.1A Spell Register into the first
public Keeper-of-Knowledge experience in the Guild Library.

## Sage, Keeper of Knowledge

Sage is established as the curator of the Guild Library's spell collection.

This phase deliberately uses a neutral `S` Keeper seal rather than inventing
Sage's physical appearance. Character artwork can be introduced separately
once its art direction is supplied and approved.

## Dedicated Spellbook

The Library now exposes:

`/library/spells`

through `LibraryController::spells()`.

The Guild Library's registered Spellbook card links directly to this route.

## Read-Only Search and Filters

`SpellbookPresenter` queries only the certified `HandbookSpellRegister`.

The Spellbook supports:

- free-text search across Marketrealm name, original spell name, source text,
  stated school and stated Calling access;
- Marketrealm Rename / Marketrealm Original filtering;
- stated spell-level filtering, including cantrips;
- stated school filtering;
- stated Calling-access filtering;
- alphabetical results;
- safe zero-result presentation.

No missing metadata is inferred.

## Source Fidelity

Canonical uncertainty remains visible in the browsing experience.

- Renamed spells can show the spell name used outside the Marketrealm.
- Missing Calling access remains explicitly "not stated".
- Source issue markers are shown as Sage's source notes.
- Bread Wall retains both handbook variants.
- Vacuum Seal retains both handbook variants and their different levels.
- The handbook's `Arificer` access label remains searchable as supplied.

## Accessibility

The Spellbook works without JavaScript.

- filters use an ordinary GET form;
- spell mechanics use native `details` / `summary` disclosure;
- result counts use `aria-live`;
- responsive layouts collapse through 1100px / 760px / 560px breakpoints;
- forced-colours mode keeps structural borders visible.

## Preservation Boundary

III.13.1B does not modify:

- character creation spell choices;
- advancement spell choices;
- Living Ledger spell presentation;
- spell-slot mechanics;
- existing Arcane Pantry data.

Those migrations remain reserved for III.13.2 — Spell Integration.

## Next Slice

**Phase III.13.1C — Spellbook Polish & Certification**

That phase can perform final browser, source-integrity and accessibility
hardening before the canonical Spellbook is allowed to feed character systems.
