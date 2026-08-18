# Phase III.12.2C — The Fighter's Battle Reserves

III.12.2C introduces GMRC's first persistent active-play class resource layer.

## Why a separate active-play layer

Permanent Character progression answers questions such as:

- Does this Fighter know Action Surge?
- How many maximum uses does Level 17 grant?
- Has Indomitable unlocked?

Active play answers a different question:

- How many uses remain **right now**?

Those two responsibilities are intentionally separated.

Advancement remains the permanent Guild-certified record.

`ActiveClassResourceState` stores only expenditure since the most recent
appropriate refresh.

## Generic resource state

The new generic state records expended uses by resource key.

Examples:

- `second-wind => 1`
- `action-surge => 1`
- `indomitable => 2`

Maximum uses are never stored in this state.

They are always derived from current certified class progression, which means a
Fighter who levels up cannot be trapped with a stale old maximum.

## Persistence

Active resource expenditure is stored in owner-scoped Character metadata:

`_gmrc_active_class_resources`

The repository resolves Characters by:

- `gmrc_character` post type;
- current WordPress user;
- `_gmrc_character_id`.

This follows the same ownership boundary used by other Character adjunct
repositories.

Legacy Characters require no migration. Missing metadata simply hydrates as a
fresh resource state.

## Fighter Battle Reserve policy

`FighterBattleReserveService` is the first class-specific policy over the
generic resource state.

It knows Fighter maximums and refresh rules.

### Second Wind

- maximum: 1
- available from Level 1
- short-rest refresh

### Action Surge

- Level 1: unavailable
- Levels 2–16: maximum 1
- Levels 17–20: maximum 2
- short-rest refresh

### Indomitable

- Levels 1–8: unavailable
- Levels 9–12: maximum 1
- Levels 13–16: maximum 2
- Levels 17–20: maximum 3
- long-rest refresh

## Short Rest

A short rest clears expenditure for:

- Second Wind
- Action Surge

It deliberately leaves Indomitable expenditure untouched.

## Long Rest

A long rest clears every Fighter Battle Reserve expenditure.

## Martial Register

III.12.2A's Martial Register now displays:

`Remaining / Maximum`

instead of only the certified maximum.

Each unlocked resource receives a **Spend 1 Use** control.

At zero remaining uses, the control is disabled and reads:

`Reserve Spent`

The panel also provides:

- Take Short Rest
- Take Long Rest

These are normal Companion POST commands, protected by the dedicated:

`gmrc_character_resources_{characterId}`

nonce contract.

After spending or refreshing, the browser returns to the Character Ledger's
Spells & Abilities tab.

## Reusable architecture

The generic state intentionally contains no Fighter terminology.

Later class policies can use the same persistence for resources such as:

- Barbarian Rage;
- Monk active resources;
- Bardic-style inspiration pools;
- Cleric or Paladin limited-use class abilities;
- Path-specific limited-use gifts.

Each class remains responsible for its own maximums and refresh policy while
the expenditure ledger stays shared.

## Browser test

A Level 4 Fighter should show:

- Second Wind — 1 / 1
- Action Surge — 1 / 1
- Indomitable — locked

Press **Spend 1 Use** on Action Surge.

After redirecting back to Spells & Abilities:

- Action Surge — 0 / 1
- button — Reserve Spent

Take a short rest:

- Second Wind — 1 / 1
- Action Surge — 1 / 1

At Level 9, spend Indomitable and then take a short rest.

Indomitable should remain spent.

A long rest should restore it.
