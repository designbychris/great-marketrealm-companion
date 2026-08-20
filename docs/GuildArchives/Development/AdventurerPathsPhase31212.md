# Phase III.12.12 — The Bard's Calling

III.12.12 promotes Bard from the registered-Calling fallback into a specialist
Calling.

## Repository-backed Bard material

The current repository already establishes that Bard:

- casts with Charisma in Arcane Pantry;
- uses the full-caster spell-slot table;
- has Bard-specific arcana such as Cutting Remark;
- has seven registered Great Marketrealm Bard College identities.

III.12.12 builds on those existing contracts without inventing College Gifts.

## Level 1 foundations

The specialist Bard Calling begins with:

- Charisma-based known-spell full casting;
- Bardic Inspiration using a d6.

## Calling progression

The permanent Bard spine includes:

- Level 2 — Jack of All Trades and Song of Rest d6;
- Level 3 — Expertise and Bard College selection;
- Level 5 — Bardic Inspiration d8 and Font of Inspiration;
- Level 6 — Countercharm;
- Level 9 — Song of Rest d8;
- Level 10 — Bardic Inspiration d10, Expertise and Magical Secrets;
- Level 13 — Song of Rest d10;
- Level 14 — Magical Secrets;
- Level 15 — Bardic Inspiration d12;
- Level 17 — Song of Rest d12;
- Level 18 — Magical Secrets;
- Level 20 — Superior Inspiration.

The shared Measure of Growth remains delegated at Levels 4, 8, 12, 16 and 19.

College Gift milestones are delegated at Levels 3, 6 and 14 for the later
College phase.

## Sanity correction

During the implementation pass, Song of Rest was checked against the intended
Calling progression.

It remains:

- d6 at Level 2;
- d8 at Level 9;
- d10 at Level 13;
- d12 at Level 17.

There is no Level 6 Song of Rest improvement.

## Bard spellcasting

Bard is registered with `SpellcastingProgressionCatalogue` as a
Charisma-based `known-spells` full caster.

Cantrips known remain:

- 2 initially;
- 3 from Level 4;
- 4 from Level 10.

The known-spell totals include the normal Magical Secrets increases at Levels
10, 14 and 18.

Bard reaches ninth-circle spellcasting at Level 17.

## Bard College

Bard now has a specialist Path definition:

- label — Bard College;
- folio — College Performance Folio;
- choice key — `bard-college`;
- selection level — 3.

The seven College identities already bundled in the repository become legal
Path candidates:

- College of the Seasoned Song;
- College of Nostalgia;
- College of Preservation;
- Charcutaire;
- College of Culinary Crescendo;
- College of Confection;
- College of Churned Verse.

## College Gift boundary

The repository currently provides College identities but does not provide
their feature mechanics.

III.12.12 therefore registers College selection without inventing College
Gifts, playstyles or feature previews.

Those belong to III.12.12B once canonical College feature definitions are
available.

## Later Bard slices

III.12.12 does not yet implement:

- a College Performance Register;
- College Gifts;
- persistent Bardic Inspiration expenditure;
- Song of Rest rolling;
- College-specific finite resources;
- player-facing performance Arts;
- Magical Secrets selection UI.

Those remain clean later phases.
