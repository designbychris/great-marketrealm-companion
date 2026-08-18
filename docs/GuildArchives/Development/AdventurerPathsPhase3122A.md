# Phase III.12.2A — The Fighter's Martial Register

III.12.2A turns Fighter progression from catalogue-only metadata into a
level-aware Character Ledger surface.

## The Martial Register

Fighter Characters now receive **The Martial Register** at the beginning of
the Spells & Abilities folio.

Non-Fighter Characters do not receive this panel.

The Register is deliberately a read model derived from already-certified
Character state. It does not introduce a second advancement history or another
mutable Character resource store.

## Current martial capability

The Register derives:

- attacks per Attack action;
- Second Wind;
- Action Surge;
- Indomitable;
- certified Martial Path;
- next major Fighter milestone.

### Second Wind

Available from Fighter Level 1.

The Register displays:

- one use;
- short-rest refresh;
- bonus-action activation;
- `1d10 + Fighter level` healing.

The existing Arcane Pantry ability remains the interactive roll surface. The
Martial Register summarises the certified class capability rather than
duplicating its dice control.

### Action Surge

- Level 1: locked
- Levels 2–16: one use
- Levels 17–20: two uses
- refresh: short rest

### Extra Attack

The Register's Attack action measure derives directly from certified Fighter
level:

- Levels 1–4: 1 attack
- Levels 5–10: 2 attacks
- Levels 11–19: 3 attacks
- Level 20: 4 attacks

### Indomitable

- Levels 1–8: locked
- Levels 9–12: 1 use
- Levels 13–16: 2 uses
- Levels 17–20: 3 uses
- refresh: long rest

## Martial Path

The Register reflects the existing Calling Path lifecycle.

Before Level 3 it reports:

`Opens at Level 3`

At Level 3 or later without a certified Path it reports:

`Awaiting Martial Path`

Once a Path is certified, the Register resolves its player-facing label from
the existing Fighter subclass catalogue.

No duplicate Fighter subclass catalogue is introduced.

## Next martial milestone

The Register gives the player a small forward-looking progression cue.

It follows the major Fighter milestones already registered in III.12.2:

- 2 — Action Surge
- 3 — Martial Path
- 5 — Extra Attack
- 9 — Indomitable
- 11 — Extra Attack
- 13 — Indomitable
- 17 — Deep Reserves
- 20 — Extra Attack

At Level 20 the Register reports martial progression as mastered.

## Architecture

New service:

`Progression/Martial/Services/FighterMartialRegisterPresenter`

The Character Controller provides its read model to the existing Character
Ledger.

The view renders the panel only when the presenter reports that the Character
is a Fighter.

This keeps the presentation extensible: later classes can receive their own
resource/register presenters without turning the Character entity into a
collection of class-specific properties.

## Resource tracking boundary

III.12.2A displays **maximum certified uses and refresh cadence**.

It does not yet persist "spent this rest" state for Action Surge or
Indomitable.

That should become a reusable active-play resource tracker rather than a
Fighter-only persistence hack, because Barbarian Rage, Monk resources,
Bardic-style resources and other future Callings will need the same lifecycle.

## Browser test

The prepared Fighter Character is ideal for checking III.12.2A.

At Level 1 the expected Martial Register is:

- Attack action: 1
- Second Wind: certified
- Action Surge: locked
- Indomitable: locked
- Martial Path: opens at Level 3
- next milestone: Level 2 Action Surge

Advancing the test Fighter will make the Register visibly change at the
appropriate certified levels.
