# Phase III.12.11D — The Cleric's Divine Arts

III.12.11D turns the certified Cleric Calling and Divine Domains into
level-gated active-play surfaces.

## Core Cleric arts

- Level 2 — Channel Divinity: Turn Undead
- Level 5 — Destroy Undead threshold display
- Level 10 — Divine Intervention display

Turn Undead spends the same shared Channel Divinity reserve used by Domain
Channel Divinity actions.

Divine Intervention remains informational because no finite resource cadence
has been certified for it.

## Domain of Sweetness

- Sweet Sanctuary displays current Cleric-level + Wisdom temporary HP.
- Sugarburst spends Channel Divinity and rolls 1d6 ally temporary HP.
- Sticky Ward exposes its Strength-save control.
- Sticky Smite rolls 1d8 radiant, improving to 2d8 at Cleric 14.
- Ascension of the Sugarcloud spends its free long-rest use. The alternative
  5th-level spell-slot route remains available through the shared spell-slot
  system rather than a duplicate counter.

## Domain of the Golden Arches

- Divine Combo Meal exposes its +2 AC support.
- Order Up spends Channel Divinity.
- Express Blessing exposes its 10-foot free movement.
- Golden Fry rolls 1d8 radiant, improving to 2d8 at Cleric 14.
- Happy Heal Hour spends its long-rest reserve.

## Domain of Dairy

- Curdled Blessing spends Channel Divinity.
- Stinky Salvation spends its long-rest reserve.
- Cultured Smite offers separate radiant and cold Diceworks rolls.
- Holy Butterstorm spends its long-rest reserve and exposes separate
  6d8 radiant and 2d8 fire rolls.

The Holy Butterstorm action receives a deliberately prominent Ledger button.

## Domain of Seasoning

- Zest spends its once-per-long-rest reserve.
- Salt the Earth spends Channel Divinity.
- Searing Seasoning offers fire, poison and acid 1d8 rolls.
- Seasoned Edge offers fire or poison Divine Strike rolls, improving to 2d8
  at Cleric 14.
- Perfect Balance spends its long-rest reserve.

## Domain of Cultivation

- Blessed Brine spends Channel Divinity and rolls 1d6 ally healing.
- Patient Culture remains a passive support reminder.
- Cultivated Faith displays the current Wisdom damage bonus.
- Sacred Vintage spends its long-rest reserve and displays its current
  Wisdom-based healing bonus.

## Domain of Fermentation

Ferment Touch exposes its three supplied branches:

- Heal Ally — 1d8 + Wisdom modifier healing.
- Preserve Corpse — static preservation; no invented roll.
- Sour Enemy — acid damage scaling from 1d8 to 4d8 at Cleric
  Levels 1 / 5 / 11 / 17.

All three consume the same Ferment Touch reserve when the player records use.

Funk of the Divine spends Channel Divinity. Its enemy damage is
2d10 + Cleric level, with separate radiant and poison choices, while its
allied bonus uses a shared d4 roll.

Spiritual Brine and Pickled Spirits remain passive/reaction reminders rather
than invented persistent resources.

Mother Culture spends its long-rest reserve and provides:

- 2d6 ally healing;
- 4d6 radiant or poison enemy damage;
- condition-cleansing and speed-halving reminders.

## Shared Diceworks

All dice-bearing Divine Arts reuse the Guild Diceworks contract:

- roll formula;
- modifier;
- kind;
- damage type;
- display label.

No Cleric-specific dice engine is introduced.

## Resource ownership

Finite Divine Arts reuse the III.12.11C `/devotion/spend` route and
`ClericSacredReserveService`.

Domain Channel Divinity actions all point to the same
`cleric-channel-divinity` resource.

## Accessibility and browser behaviour

The Divine Arts surface:

- wraps long sacred feature names and text;
- uses native roll and submit buttons;
- stacks to one column on narrow screens;
- supports reduced-motion preferences;
- remains visible in forced-colours mode.

The Holy Butterstorm control receives a larger minimum action height but no
motion-dependent effect.
