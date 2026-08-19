# Phase III.12.5 — The Monk's Calling

Phase III.12.5 promotes Monk from the generic Calling foundation to its own
specialist progression.

## Repository-derived Monastic Ways

The supplied player catalogue already registers six Monk Ways:

1. Way of the Spun Cloud
2. Way of the Neon Crunch
3. Way of the Vacuum Seal
4. Way of the Simmering Soul
5. Way of the Whirling Utensil
6. Way of the Spongecake Soul

Their catalogue records currently contain names, descriptions and parent
relationships, but no trait definitions.

III.12.5 therefore wires those six identities into the shared Path machinery
without fabricating Way Gifts yet.

## Specialist Monk progression

The Companion now carries permanent Monk advancement reference metadata.

Major milestones include:

- Level 2 — Discipline and Unarmoured Movement
- Level 3 — Deflect Missiles and Monastic Way choice
- Level 4 — Slow Fall
- Level 5 — Extra Attack and Stunning Strike
- Level 6 — Disciplined Strikes
- Level 7 — Evasion and Stillness of Mind
- Level 10 — Purity of Body
- Level 13 — Tongue of Sun and Moon
- Level 14 — Diamond Soul
- Level 15 — Timeless Body
- Level 18 — Empty Body
- Level 20 — Perfect Self

The Monk's starting Level 1 martial identity remains part of Character
creation and is not re-certified during Level 2+ advancement.

## Monastic Way

Monk now registers a shared specialist Path progression:

- label: **Monastic Way**
- folio: **Monastic Way Folio**
- choice key: `monastic-way`
- selection level: 3

The shared `PathCandidateCatalogue` supplies the six repository-defined Ways.

## Measure of Growth

Monk delegates growth decisions to the existing shared Measure of Growth at:

- Level 4
- Level 8
- Level 12
- Level 16
- Level 19

## Reserved Way Gifts

Because the source catalogue does not yet define Monk Way traits,
III.12.5 reserves later Path Gift hand-offs at:

- Level 6
- Level 11
- Level 17

The first Level 3 Way Gift will be introduced in III.12.5B when all six
Great Marketrealm Ways receive explicit gift definitions.

## Active-play boundary

III.12.5 deliberately does not persist or spend Discipline yet.

Planned slices:

- III.12.5A — The Monk's Discipline Register
- III.12.5B — The Monk's Ways
- III.12.5C — The Monk's Discipline Reserves
- III.12.5D — The Monk's Martial Techniques
- III.12.5E — The Monk's Final Seal

## Framework audit

Monk becomes the fifth specialist Calling.

The specialist set becomes:

- Barbarian
- Fighter
- Monk
- Rogue
- Wizard

Monk remains a non-baseline-spellcasting specialist with a Calling Path.
