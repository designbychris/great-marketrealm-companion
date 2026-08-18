# Phase III.12.3A — The Barbarian's Rage Register

III.12.3A turns the Barbarian specialist progression into a visible,
level-aware Character Ledger surface.

## Scope

This phase is deliberately presentation-only.

It derives Barbarian capability from:

- certified Character level;
- the existing persisted Calling Path;
- the progression milestones introduced in III.12.3.

It does **not** yet persist:

- Rages spent;
- whether Rage is currently active;
- Rage duration remaining.

Those active-play responsibilities remain reserved for III.12.3C.

## The Rage Register

Barbarian Characters now receive **The Rage Register** in the Spells &
Abilities folio.

Non-Barbarian Characters do not receive it.

The Register presents:

- Rage capacity;
- Rage damage bonus;
- attacks per Attack action;
- Fast Movement bonus;
- Brutal Critical dice;
- current Primal Path;
- major Barbarian feature unlocks;
- next primal milestone.

## Rage capacity

The Register derives the certified Rage capacity from Barbarian level:

- Levels 1–2 — 2 Rages
- Levels 3–5 — 3 Rages
- Levels 6–11 — 4 Rages
- Levels 12–16 — 5 Rages
- Levels 17–19 — 6 Rages
- Level 20 — unlimited

The UI renders unlimited Rage with an infinity mark.

This is maximum certified capacity only. III.12.3C will add remaining-use
tracking.

## Rage damage

The Register derives the Rage damage bonus:

- Levels 1–8 — +2
- Levels 9–15 — +3
- Levels 16–20 — +4

## Extra Attack and Fast Movement

At Level 5:

- attacks per Attack action changes from 1 to 2;
- Fast Movement displays +10 ft.

## Brutal Critical

The Register displays the extra critical weapon dice:

- Levels 1–8 — not yet
- Levels 9–12 — +1 die
- Levels 13–16 — +2 dice
- Levels 17–20 — +3 dice

## Major feature cards

The Register includes level-aware certification cards for:

- Reckless Attack
- Danger Sense
- Feral Instinct
- Relentless Rage
- Persistent Rage
- Indomitable Might
- Primal Champion

Locked features remain visible but subdued so the player can see the shape of
future Barbarian progression.

## Primal Path

Before Level 3:

`Opens at Level 3`

At Level 3+ before certification:

`Awaiting Primal Path`

After certification, the player-facing label is resolved through the existing
Barbarian Path catalogue.

No duplicate Path-name table is created.

## Next milestone

The Register gives a forward-looking progression cue through the major
Barbarian milestones introduced in III.12.3.

At Level 20 it reports that primal progression is mastered.

## Architecture

New service:

`Progression/Primal/Services/BarbarianRageRegisterPresenter`

The Character Controller supplies its read model to the existing Ledger.

The view renders the panel only when the presenter reports Barbarian support.

This establishes a separate `Primal` presentation family rather than forcing
Barbarian state through Fighter-specific Martial Register classes.

## Browser test

For a fresh Level 1 Barbarian, expect:

- Rages — 2
- Rage Damage — +2
- Attack action — 1
- Fast Movement — Not yet
- Brutal Critical — Not yet
- Reckless Attack — Locked
- Danger Sense — Locked
- Primal Path — Opens at Level 3
- Next milestone — Level 2

At Level 2:

- Reckless Attack — Certified
- Danger Sense — Certified

At Level 3:

- Rage capacity — 3
- Primal Path — available

At Level 5:

- Attack action — 2
- Fast Movement — +10 ft
