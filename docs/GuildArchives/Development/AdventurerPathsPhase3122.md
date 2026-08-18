# Phase III.12.2 — The Fighter's Calling

Phase III.12 gains its first non-Wizard specialist Calling.

## Purpose

Fighter is the framework proof that specialist advancement can be modelled
without depending on Wizard spellbook machinery.

The implementation stays within GMRC's existing advancement architecture:

- `ClassProgressionCatalogue`
- `CallingFolio`
- `PathProgressionCatalogue`
- `PathFolio`
- shared Measure of Growth delegation
- existing Character subclass metadata

No new Character persistence format is required in III.12.2.

## Fighter specialist progression

`FighterProgression` now owns Fighter advancement reference metadata from
levels 2–20.

The Calling catalogue records the Fighter's major automatic martial
milestones, including:

- Action Surge;
- Extra Attack progression;
- Indomitable progression.

These automatic entries are reference metadata at this stage. Resolving the
Calling Folio does not silently mutate permanent Character abilities.

The shared `CallingFolio` now explains when a level contains automatic Calling
gains instead of incorrectly describing such a level as having no specialist
activity.

## Martial Path

Fighter now has a specialist Path definition:

- label: **Martial Path**
- selection level: **3**
- choice key: `fighter-martial-path`

The shared Path Folio and Path candidate catalogue do the rest.

The bundled Character catalogue already contains six Fighter paths:

1. Discontinued Lineage
2. Butcher
3. The Carver
4. Cutlery Knight
5. The Vineblade
6. Shelf Sentinel

III.12.2 does not duplicate those choices into progression code.

## Measure of Growth

Fighter's additional growth milestones are explicitly delegated to the
existing shared growth folio at levels:

- 4
- 6
- 8
- 12
- 14
- 16
- 19

This is important architecture-wise: Fighter does not need its own copy of
ability/talent advancement machinery.

## Martial Path feature milestones

Levels 7, 10, 15 and 18 identify a future `path-gifts` responsibility.

They are deliberately marked for **III.12.2B**.

The current bundled Fighter subclass catalogue contains path identity and
descriptions but does not yet contain enough path-feature rules to fabricate
per-path gifts safely.

III.12.2 therefore records the hand-off without inventing subclass abilities.

## Spellcasting boundary

Fighter does not gain a baseline specialist spellcasting definition simply by
becoming a specialist Calling.

This proves the III.12 capability system is compositional:

- Wizard: specialist advancement + spellcasting + Path
- Fighter: specialist advancement + Path
- other Callings: current foundation state

## Class Framework Audit

The III.12.1 audit now promotes Fighter automatically.

Expected implementation state after III.12.2:

- specialist Callings: 2
- foundation Callings: 13
- Wizard: specialist
- Fighter: specialist

The audit implementation itself is unchanged; only its regression expectations
move with the newly registered capability.

## Next Fighter slice

A sensible follow-on is **III.12.2A — The Fighter's Martial Register**.

That can turn the Fighter's automatic progression metadata into richer
Character Ledger presentation and reusable martial resource tracking before
III.12.2B begins encoding individual Martial Path gifts.
