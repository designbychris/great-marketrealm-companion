# Phase III.13.1C — Spellbook Polish & Certification

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.13.1C is the hardening and final-certification slice for Sage's
Spellbook. It adds no new spells and does not begin character integration.

## Canonical Register Seal

The certified Spellbook remains:

- 71 canonical spell identities;
- 73 source variants;
- 29 Marketrealm renamed/reskinned spells;
- 42 original Marketrealm spells.

No source gaps are filled with general D&D knowledge.

Bread Wall retains both conflicting handbook variants.

Vacuum Seal retains both supplied variants and their different source levels.

The handbook's `Arificer` label remains visible/searchable rather than being
silently corrected.

## Unknown Metadata as First-Class Knowledge

The Spellbook can now explicitly filter:

- Level not stated;
- School not stated;
- Calling access not stated.

Sage's source notes identify exactly which canonical metadata is unresolved
instead of showing only an uncertainty count.

## Query Hardening

Search and filter state is sanitised before it reaches the result surface.

Unknown or malformed kind, level, school and Calling-access filters fall back
to an unfiltered state rather than being treated as canonical values.

Only levels, schools and access labels actually present in the certified
register can become active filters.

## Accessibility & Browser Hardening

The Spellbook keeps its no-JavaScript core.

- GET search form;
- native `details` / `summary`;
- visible `:focus-visible` outlines;
- reduced-motion protection;
- forced-colours protection;
- responsive 1100px / 760px / 560px layouts;
- `aria-live` result count.

## Preservation Boundary

III.13.1C still does not alter:

- Character Creation spell choices;
- advancement spell choices;
- Living Ledger spell presentation;
- spell slots or active spell resources;
- Arcane Pantry data.

## Certification Meaning

Once PHPUnit and browser review pass, **Sage's Spellbook is complete as a
standalone Guild Library system**.

The next phase is:

**III.13.2 — Spell Integration**

That phase may begin replacing duplicated character-facing spell knowledge
with canonical queries into the Keeper of Knowledge's certified register.
