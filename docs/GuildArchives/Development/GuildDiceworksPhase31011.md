# Phase III.10.11 — Spell & Ability Roll Scaling

The Arcane Pantry now resolves scalable spell and feature formulas in PHP before Diceworks receives them.

## Scaling resolver

`ArcaneRollScalingResolver` provides three PHP-owned axes:

- character level;
- spell-slot level;
- feature rank.

`ArcaneAbilityDefinition` can declare formula maps for any of these axes. The resolver selects the highest eligible threshold and returns the resolved formula plus transparent metadata.

JavaScript does not know the level thresholds and does not calculate spell progression.

## Character-level scaling

Damaging cantrips in the current catalogue now carry character-level scaling. For example, Produce Spark resolves as:

- levels 1–4: `1d10`
- levels 5–10: `2d10`
- levels 11–16: `3d10`
- levels 17–20: `4d10`

The same architecture is used by other scalable cantrips and selected level-driven class features such as Sneak Attack and Martial Arts.

## Slot-level preparation

Restorative Preserve and Market Missile now declare slot-level formula maps.

The current Ledger still resolves them at their base spell level because III.10.11 does not add a higher-slot casting selector. The complete slot option map is nevertheless carried in PHP presentation metadata so a later casting workflow can request a different slot level without rewriting Diceworks.

## Feature-rank preparation

The resolver also supports `featureRankScaling`. No current Ledger UI selects a feature rank yet, but the axis is tested and ready for later ranked/subclass mechanics.

## Arcane Pantry

Scalable roll cards display their current PHP-resolved formula and identify whether the current result came from adventurer-level scaling or is prepared for higher-slot scaling.

The roll trigger exposes:

- resolved formula;
- base formula;
- scaling source;
- resolved threshold.

Diceworks consumes only the resolved formula.

## Quick Roll compatibility

Character Quick Roll identity no longer includes the mutable roll formula. This prevents a favourite from becoming stale when progression changes `1d10` into `2d10`.

Older saved favourites receive a label-based compatibility lookup if their legacy key no longer matches the current trigger.

## Boundaries

This phase does not spend spell slots, choose higher-level slots, mutate spell preparation, or add target application. Slot selection remains future work; targeting begins in III.10.12.
