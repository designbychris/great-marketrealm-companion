# Phase III.12.9 — The Ranger's Calling

III.12.9 promotes Ranger from the generic registered-Calling fallback into a
specialist Calling.

## Repository-backed Ranger material

The repository already provides two important Ranger capabilities:

- `Favoured Mark` is present in the Arcane Ability Catalogue as a Ranger
  feature.
- Arcane Pantry already treats Ranger as a Wisdom-based half caster and uses
  the shared Paladin/Ranger half-caster spell-slot progression.

III.12.9 builds on those existing contracts rather than replacing them.

## Level-one foundations

Ranger now has explicit Calling foundations for:

- Favoured Mark
- Natural Explorer

## Calling progression

The specialist Ranger reference includes:

- Level 2 — Fighting Style and Spellcasting
- Level 3 — Primeval Awareness
- Level 5 — Extra Attack
- Level 6 — Favoured Mark Improvement
- Level 8 — Land's Stride
- Level 10 — Hide in Plain Sight
- Level 14 — Vanish and final Favoured Mark Improvement
- Level 18 — Feral Senses
- Level 20 — Foe Slayer

The shared Measure of Growth remains delegated at Levels 4, 8, 12, 16 and 19.

## Spellcasting model

Ranger is registered with the shared `SpellcastingProgressionCatalogue` as a
Wisdom-based `known-spells` half caster.

Spellcasting begins at Level 2.

The specialist reference records:

- spells known;
- no baseline cantrips;
- newly learned spell count;
- highest available spell circle.

The spell-circle thresholds are:

- Level 2 — 1st circle
- Level 5 — 2nd circle
- Level 9 — 3rd circle
- Level 13 — 4th circle
- Level 17 — 5th circle

## Ranger path boundary

The current bundled player catalogue contains zero Ranger subclass entries.

III.12.9 therefore does not register a Ranger `PathProgressionCatalogue`
definition or show a path selector with no legal choices behind it.

This is deliberate.

When Ranger subclasses are added to the catalogue, a later Ranger slice can
register the Ranger path and gifts without undoing the specialist Calling or
spellcasting work completed here.

## Later Ranger slices

III.12.9 does not yet implement:

- a Ranger Field Register;
- Favoured Mark active resource state;
- Ranger subclass/path selection;
- subclass gifts;
- Ranger-specific active spell controls.

Those remain clean later slices.
