# Phase III.12.4C — The Rogue's Cunning Actions

III.12.4C turns Cunning Action from read-only Rogue progression into a usable
active-play Ledger surface.

## Rule boundary

Cunning Action is not a finite resource.

At Level 2+, the Rogue can use the feature every turn as a bonus action.

The Companion therefore does **not** introduce:

- remaining uses;
- rest recovery;
- server persistence;
- a resource repository entry.

This is deliberately different from Fighter reserves and Barbarian Rage.

## Dash

**Use Dash** is an action declaration.

Dash does not require a dice roll, so the Companion does not invent one.

Selecting the control marks Dash as the currently declared Cunning Action in
the browser and announces the choice through an ARIA live region.

The declaration is not persisted because the feature refreshes every turn.

## Disengage

**Use Disengage** follows the same declaration pattern.

No roll and no limited resource are invented.

## Hide

Hide is the Cunning Action option that can genuinely require a roll.

The **Roll Hide** control reuses Guild Diceworks with the Character's actual:

- Dexterity (Stealth) modifier;
- Stealth proficiency state;
- Stealth expertise state when present.

The roll remains Normal by default. The player can still select Advantage or
Disadvantage in Guild Diceworks when the scene requires it.

Whether hiding is possible remains a table/scene decision; the Companion does
not claim that pressing the button automatically makes the Rogue hidden.

## Shared active-play lesson

Rogue becomes the reference implementation for a class action that is:

- repeatable every turn;
- not a resource;
- partly declarative;
- partly dice-backed.

This prevents the Companion from treating every interactive class mechanic as
an expendable counter.

## Accessibility

Dash and Disengage declaration controls:

- are native buttons;
- expose selection with `aria-pressed`;
- update an `aria-live="polite"` status;
- retain visible focus treatment.

Hide stays a native Guild Diceworks trigger.

The layout falls back to one column on narrower displays and preserves
forced-colours borders.

## Deliberate deferral

III.12.4C does not implement:

- Sneak Attack once-per-turn state;
- attack qualification;
- Uncanny Dodge reaction use;
- Evasion resolution;
- Stroke of Luck expenditure.

Those belong to:

**III.12.4D — The Rogue's Precision & Reactions**

## Browser test

At Rogue Level 1:

- Dash / Disengage / Hide remain locked.

At Rogue Level 2+:

1. Confirm the Cunning Actions panel says `Bonus action · Every turn`.
2. Press **Use Dash**.
3. Confirm the live status announces Dash.
4. Press **Use Disengage**.
5. Confirm the selection/status moves to Disengage.
6. Press **Roll Hide**.
7. Guild Diceworks should open with the Character's real Stealth modifier.
8. Confirm no remaining-use counter appears.
9. Refreshing the Ledger should not preserve a Dash/Disengage declaration,
   because Cunning Action renews every turn.
