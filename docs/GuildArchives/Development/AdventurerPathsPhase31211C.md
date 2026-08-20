# Phase III.12.11C — The Cleric's Sacred Reserves

III.12.11C adds persistent active-play expenditure for Channel Divinity and
the finite Divine Domain resources explicitly certified in III.12.11B.

## Channel Divinity

Channel Divinity is one shared Cleric resource.

- Level 2 — 1 use per short or long rest
- Level 6 — 2 uses per short or long rest
- Level 18 — 3 uses per short or long rest

Turn Undead and every Domain Channel Divinity option spend this same pool.
The Companion does not create a second counter for Sugarburst, Order Up,
Curdled Blessing, Salt the Earth, Blessed Brine or Funk of the Divine.

## Domain of Sweetness

- Ascension of the Sugarcloud free use — once per long rest from Level 17.
- The supplied alternative of expending a 5th-level spell slot remains a
  separate route for the Divine Arts phase.

## Domain of the Golden Arches

- Happy Heal Hour — once per long rest from Level 17.

## Domain of Dairy

- Stinky Salvation — once per long rest from Level 6.
- Holy Butterstorm — once per long rest from Level 17.

## Domain of Seasoning

- Zest — once per long rest from Level 1.
- Perfect Balance — once per long rest from Level 17.

## Domain of Cultivation

- Sacred Vintage — once per long rest from Level 17.

## Domain of Fermentation

- Ferment Touch — Wisdom modifier uses per long rest, with a minimum usable
  pool of one.
- The ally-healing branch remains additionally limited to once per creature
  per long rest. That per-target history is documented rather than falsely
  represented by a character-wide counter.
- Mother Culture — once per long rest from Level 17.

Spiritual Brine's once-per-round reaction is encounter cadence, not a
rest-based reserve, so no persistent counter is invented.

## Divine Intervention boundary

The current certified Cleric source establishes Divine Intervention as a
Calling milestone but does not define a Companion rest/use cadence for it.
III.12.11C therefore does not invent a Divine Intervention reserve.

## Route ownership

The repository already uses `/sacred/*` for Paladin.

Cleric therefore receives a dedicated `/devotion/*` route family:

- `/characters/{id}/devotion/spend`
- `/characters/{id}/devotion/rest`

This prevents two sacred Callings from sharing controller semantics while
retaining their distinct class-resource services.

## Rest ownership

A Sacred Short Rest restores only resources explicitly marked for short-rest
recovery — currently Channel Divinity.

A Sacred Long Rest restores all Cleric Sacred Reserves. The controller then
restores shared Cleric spell slots through `SharedSpellSlotReserveService`,
preserving clean ownership.

## Sacred Domain Register

The III.12.11A Register now exposes live Sacred Reserve state and remaining
Channel Divinity uses.

## Active-play boundary

III.12.11C tracks expenditure only.

It does not yet roll Sugarburst temporary HP, Holy Butterstorm damage, Ferment
Touch healing/damage, Mother Culture effects, Divine Strikes, Turn Undead or
other sacred techniques.

Those belong to III.12.11D — The Cleric's Divine Arts.
