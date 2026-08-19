# Phase III.12.4 — The Rogue's Calling

Phase III.12.4 promotes Rogue from the generic Calling foundation to its own
specialist progression.

## Repository-derived Rogue material

The supplied player catalogue already registers six Rogue Archetypes:

1. The Cheetoblade
2. Spiceblade
3. The Breadknife
4. Mastermind of the Aisles
5. Aisle Stalker
6. Taffy Trickster

The repository currently supplies each Archetype's identity and short
description, but its `traits` arrays are empty.

III.12.4 therefore uses those six repository-defined identities as the
available Archetype choices without silently fabricating Archetype Gifts.

## Companion progression reference

III.12.4 newly encodes the Rogue's core level progression as Companion
reference metadata. This progression was not supplied by the attached player
catalogue.

The progression includes:

- Level 2 — Cunning Action
- Level 3 onward — Sneak Attack scaling on odd levels
- Level 5 — Uncanny Dodge
- Level 6 — Expertise
- Level 7 — Evasion
- Level 11 — Reliable Talent
- Level 14 — Blindsense
- Level 15 — Slippery Mind
- Level 18 — Elusive
- Level 20 — Stroke of Luck

Sneak Attack progresses from 2d6 at Level 3 to 10d6 at Level 19 in the
advancement reference.

The Rogue's starting Level 1 abilities are not re-certified during Level 2+
advancement.

## Rogue Archetype

Rogue now registers a shared specialist Path progression:

- label: **Rogue Archetype**
- folio: **Rogue Archetype Folio**
- choice key: `rogue-archetype`
- selection level: 3

The shared `PathCandidateCatalogue` supplies the six repository-defined Rogue
Archetypes.

## Measure of Growth

Rogue delegates growth decisions to the existing shared Measure of Growth at:

- Level 4
- Level 8
- Level 10
- Level 12
- Level 16
- Level 19

No Rogue-specific ability score or talent system is introduced.

## Reserved Archetype Gifts

Because the source catalogue does not currently define Archetype traits,
III.12.4 reserves later Archetype feature hand-offs at:

- Level 9
- Level 13
- Level 17

The Level 3 first Archetype Gifts will be introduced in III.12.4B only after
all six Great Marketrealm Archetypes receive explicit definitions.

## Active-play boundary

III.12.4 deliberately does not make Sneak Attack, Cunning Action, Uncanny
Dodge, or other contextual Rogue mechanics interactive yet.

Planned slices remain:

- III.12.4A — The Rogue's Cunning Register
- III.12.4B — The Rogue's Archetypes
- III.12.4C — The Rogue's Cunning Actions
- III.12.4D — The Rogue's Precision & Reactions
- III.12.4E — The Rogue's Final Seal

## Framework audit

Rogue becomes the fourth specialist Calling.

The specialist set becomes:

- Barbarian
- Fighter
- Rogue
- Wizard

Rogue remains a non-baseline-spellcasting specialist with a Calling Path.
