# Phase III.12.10 — The Druid's Calling

III.12.10 promotes Druid from the generic registered-Calling fallback into a
specialist Calling.

## Repository-backed Druid material

The repository already recognises Druid as:

- a Wisdom-based caster in Arcane Pantry;
- a full caster for shared spell-slot progression;
- a user of Druid-specific arcana such as Vine Lash;
- a Calling with six bundled Circle identities.

III.12.10 builds on those existing contracts.

## Level-one foundations

The specialist Druid Calling establishes:

- Druidic;
- Wisdom-based prepared spellcasting.

## Calling progression

The permanent Druid spine includes:

- Level 2 — Wild Shape and Druid Circle;
- Level 4 — Wild Shape Improvement;
- Level 8 — Wild Shape Improvement;
- Level 18 — Timeless Body and Beast Spells;
- Level 20 — Archdruid.

The shared Measure of Growth remains delegated at Levels 4, 8, 12, 16 and 19.

Circle gifts are delegated at Levels 6, 10 and 14 for a later Druid Circle
slice.

## Prepared spellcasting

Druid is registered with `SpellcastingProgressionCatalogue` as a Wisdom-based
`prepared-spells` full caster.

The progression does not treat prepared spells as permanently known spells.

Instead it records the preparation rule as:

`Druid level + Wisdom modifier`, minimum one prepared spell.

Baseline Druid cantrips remain:

- 2 at early levels;
- 3 from Level 4;
- 4 from Level 10.

Druid reaches the normal full-caster spell-circle thresholds and 9th-circle
magic from Level 17.

## Druid Circle

Druid now has a specialist Path definition:

- label — Druid Circle;
- folio — Circle Grove Folio;
- choice key — `druid-circle`;
- selection level — 2.

The six Circle identities already bundled in the catalogue become legal Path
candidates:

- Circle of Eating Fresh;
- Circle of the Groveflame;
- Circle of the Deep Soil;
- Circle of the Compost;
- Circle of Curdle;
- Circle of the Churn.

## Circle Gift boundary

The current repository contains Circle names but does not yet contain their
actual feature progressions.

III.12.10 therefore registers Circle identity and selection without inventing
Circle Gifts.

A later Druid slice can add the exact Circle feature material to the shared
Path Gifts catalogue.

## Later Druid slices

III.12.10 does not yet implement:

- a Druid Grove Register;
- Circle feature descriptions or previews;
- Wild Shape expenditure;
- Wild Shape form validation;
- Circle-specific resources;
- Circle-specific active techniques;
- prepared-spell selection UI.

Those remain clean later slices.
