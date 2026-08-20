# Phase III.12.11 — The Cleric's Calling

III.12.11 promotes Cleric from the generic registered-Calling fallback into a
specialist Calling.

## Repository-backed Cleric material

The repository already recognises Cleric as:

- a Wisdom-based caster in Arcane Pantry;
- a full caster for shared spell-slot progression;
- a user of Cleric-specific arcana such as Sacred Brine;
- a Calling with six bundled Divine Domain identities.

III.12.11 builds on those existing contracts.

## Level-one foundations

The specialist Cleric Calling establishes:

- prepared Wisdom spellcasting;
- Divine Domain selection at Level 1.

## Calling progression

The permanent Cleric spine includes:

- Level 2 — Channel Divinity and Turn Undead;
- Level 5 — Destroy Undead, CR 1/2;
- Level 6 — two Channel Divinity uses;
- Level 8 — Destroy Undead, CR 1;
- Level 10 — Divine Intervention;
- Level 11 — Destroy Undead, CR 2;
- Level 14 — Destroy Undead, CR 3;
- Level 17 — Destroy Undead, CR 4;
- Level 18 — three Channel Divinity uses;
- Level 20 — Divine Intervention Improvement.

The shared Measure of Growth remains delegated at Levels 4, 8, 12, 16 and 19.

Domain feature milestones are delegated at Levels 2, 6, 8 and 17 for the
dedicated Divine Domains slice.

## Prepared spellcasting

Cleric is registered with `SpellcastingProgressionCatalogue` as a Wisdom-based
`prepared-spells` full caster.

The progression records the preparation rule as:

`Cleric level + Wisdom modifier`, minimum one prepared spell.

Baseline Cleric cantrips remain:

- 3 at early levels;
- 4 from Level 4;
- 5 from Level 10.

Cleric reaches normal full-caster spell-circle thresholds and ninth-circle
magic from Level 17.

## Divine Domain

Cleric now has a specialist Path definition:

- label — Divine Domain;
- folio — Sacred Domain Folio;
- choice key — `cleric-domain`;
- selection level — 1.

The six Domain identities already bundled in the catalogue become legal Path
candidates:

- Domain of Sweetness;
- Domain of the Golden Arches;
- Domain of Dairy;
- Domain of Seasoning;
- Domain of Cultivation;
- Domain of Fermentation.

## Domain Gift boundary

The current repository contains Domain identities but not their full
feature progressions.

III.12.11 therefore registers Domain identity and selection without inventing
Domain Gifts. Those belong to III.12.11B once their source definitions are
available.

## Later Cleric slices

III.12.11 does not yet implement:

- a Sacred Domain Register;
- Domain feature descriptions or previews;
- Channel Divinity expenditure;
- Divine Intervention expenditure;
- Domain-specific resources;
- Domain-specific active techniques;
- prepared-spell selection UI.

Those remain clean later slices.
