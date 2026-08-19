# Phase III.12.3C — The Barbarian's Rage Reserves

III.12.3C turns Rage into persistent active-play state.

## Separation of responsibilities

Certified Barbarian progression remains responsible for maximum Rage capacity.

The active-play layer records only:

- how many Rages have been expended;
- whether Rage is currently active.

This prevents stale maximums when a Barbarian advances in level.

## Generic active conditions

`ActiveClassConditionState` introduces reusable boolean active-play conditions.

The first condition is:

`rage`

The companion can later reuse this layer for class or Path states that need a
persistent on/off lifecycle.

Owner-scoped condition persistence uses:

`_gmrc_active_class_conditions`

## Rage Reserve policy

`BarbarianRageReserveService` owns Barbarian-specific Rage policy.

Entering Rage:

1. verifies the Character is a Barbarian;
2. rejects activation when already Raging;
3. spends one Rage reserve unless Rage is unlimited;
4. activates the persisted `rage` condition.

Ending Rage:

- clears the active condition;
- does not refund the Rage that was spent.

Long Rest:

- restores expended Rage reserves;
- ends any currently active Rage.

## Rage capacity

Maximum Rage uses remain derived from certified level:

- Levels 1–2: 2
- Levels 3–5: 3
- Levels 6–11: 4
- Levels 12–16: 5
- Levels 17–19: 6
- Level 20: unlimited

Level 20 does not decrement the resource ledger when Rage begins.

## Rage Register

The Rage badge now shows:

`remaining / maximum`

rather than only maximum capacity.

While Rage is active the Register displays:

`🔥 RAGING`

and prominently repeats the current Rage damage bonus.

The player receives:

- **🔥 Enter Rage**
- **End Rage**
- **Take Long Rest**

The Enter Rage control is unavailable when no finite reserves remain.

## Persistence and refresh

Refreshing or revisiting the Character Ledger does not restore Rage uses or end
the active state.

Only explicit End Rage or Long Rest changes the active condition.

## Security

Rage commands are normal Companion POST actions protected by:

`gmrc_character_rage_{characterId}`

The active resource and condition repositories remain owner-scoped.

## Presentation

Active Rage receives a restrained pulse treatment.

`prefers-reduced-motion` removes the animation.

Forced-colours presentation retains visible borders and controls.

## Browser test

For the current Level 3 Barbarian:

1. Confirm `3/3 Rages remaining`.
2. Press **🔥 Enter Rage**.
3. The Ledger returns to Spells & Abilities.
4. Confirm `2/3 Rages remaining`.
5. Confirm the Register says `🔥 RAGING`.
6. Refresh the browser and confirm both states persist.
7. Press **End Rage**.
8. Confirm Rage becomes dormant but remains `2/3`.
9. Enter Rage again: `1/3`.
10. Press **Take Long Rest**.
11. Confirm Rage is dormant and capacity returns to `3/3`.

At Level 20, Enter Rage must leave the display unlimited and must not spend a
finite reserve.
