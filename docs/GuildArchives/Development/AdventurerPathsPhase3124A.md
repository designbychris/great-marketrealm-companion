# Phase III.12.4A — The Rogue's Cunning Register

III.12.4A turns the Rogue specialist progression into a visible, level-aware
Character Ledger surface.

## Scope

This phase is read-only.

It derives Rogue capability from:

- certified Character level;
- the existing persisted Rogue Archetype.

It does not yet persist:

- whether Sneak Attack has been used this turn;
- Cunning Action choice/state;
- Uncanny Dodge reaction use;
- Stroke of Luck expenditure.

Those contextual active-play responsibilities remain reserved for III.12.4C
and III.12.4D.

## The Cunning Register

Rogue Characters now receive **The Cunning Register** in the Spells &
Abilities folio.

Non-Rogue Characters do not receive it.

The Register presents:

- current Sneak Attack dice;
- Sneak Attack frequency;
- Cunning Action status;
- Dash / Disengage / Hide reminder;
- major Rogue feature unlocks;
- current Rogue Archetype;
- next major Rogue milestone.

## Sneak Attack

Sneak Attack is derived directly from Rogue level:

- Level 1: 1d6
- Level 3: 2d6
- Level 5: 3d6
- Level 7: 4d6
- Level 9: 5d6
- Level 11: 6d6
- Level 13: 7d6
- Level 15: 8d6
- Level 17: 9d6
- Level 19+: 10d6

The Register labels Sneak Attack as **Once per turn** but does not yet track
that turn usage.

## Cunning Action

At Level 2 the Register certifies Cunning Action and reminds the player of its
three core options:

- Dash
- Disengage
- Hide

Interactive action handling remains for III.12.4C.

## Major feature cards

The Register includes level-aware cards for:

- Uncanny Dodge
- Expertise
- Evasion
- Reliable Talent
- Blindsense
- Slippery Mind
- Elusive
- Stroke of Luck

Locked features remain visible but subdued so the player can see the future
shape of Rogue progression.

## Rogue Archetype

Before Level 3:

`Opens at Level 3`

At Level 3+ before certification:

`Awaiting Rogue Archetype`

After certification, the label is resolved from the repository-backed
`PathCandidateCatalogue`.

No duplicate Rogue Archetype name table is introduced.

## Next milestone

The Register gives the player a forward-looking Rogue progression cue.

At Level 20 the Register reports that Rogue progression is mastered.

## Architecture

New service:

`Progression/Cunning/Services/RogueCunningRegisterPresenter`

The Character Controller supplies the read model to the existing Ledger.

The view renders it only when the presenter reports Rogue support.

This establishes a separate `Cunning` presentation family rather than forcing
Rogue through Fighter or Barbarian-specific classes.

## Browser test

For a Level 1 Rogue, expect:

- Sneak Attack: 1d6
- Once per turn
- Cunning Action: locked
- Rogue Archetype: Opens at Level 3
- Next milestone: Level 2 — Cunning Action

At Level 2:

- Cunning Action becomes certified
- Dash / Disengage / Hide are visible

At Level 3:

- Sneak Attack becomes 2d6
- Rogue Archetype becomes available

At Level 5:

- Sneak Attack becomes 3d6
- Uncanny Dodge becomes certified
