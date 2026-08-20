# Phase III.12.10D — The Druid's Grove Arts

III.12.10D turns the six certified Druid Circles into level-gated active-play
surfaces.

## Circle of the Compost

A Level 2 Compost Druid immediately sees Rotbound Affinity and Compost Surge.

Compost Surge preserves both supplied reactions:

- Reclaim Vitality — 1d6 + Wisdom modifier healing.
- Recycle into Harm — static necrotic damage equal to Druid level.

The latter deliberately has no dice button because the source supplied a
static value.

Later Compost milestones expose Mulchborn 2d8 poison, Bloom of Decay 4d6
poison and 1d6 healing, slot-free Blight / Insect Plague resource actions,
and Avatar of the Rotten Grove using Wild Shape with separate 2d10
bludgeoning and 2d6 poison Mulch Slam rolls.

## Other Circles

The same surface exposes source-defined mechanics for Eating Fresh,
Groveflame, Deep Soil, Curdle and Churn.

Notable protected values include:

- Crisp Aura — 1 HP per round;
- Scorching Bloom — 4d8 fire;
- Living Earthquake — fixed DC 16;
- Bacteria Bloom — Druid level + Wisdom modifier temporary HP;
- Blessing of the Creammother — 1d6 temporary HP.

## Resource ownership

Grove Arts do not create another reserve ledger. Buttons reuse the
III.12.10C `/primal/spend` route and the existing Primal Reserve state.

Wild Shape transformations consume the Wild Shape reserve when the supplied
feature says so.

## Diceworks

All dice-bearing Grove Arts use the shared Guild Diceworks contract with
separate formula, modifier, kind and damage-type attributes.

Static outcomes remain static rather than being converted into invented dice.

## Accessibility

The Grove Arts surface uses native buttons and server-backed forms, wraps
long Circle text, stacks on narrower screens, and keeps reduced-motion and
forced-colours support.
