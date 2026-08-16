# Phase III.10.9 — Situational Modifiers

Guild Diceworks now supports temporary table-level adjustments without changing the adventurer's certified character maths.

## Next-roll adjustment

The Diceworks tray exposes a **Situational Adjustment** panel with:

- flat modifier from -20 to +20;
- optional bonus die: d4, d6, d8, d10 or d12;
- common -2, -1, +1 and +2 shortcuts.

The adjustment is consumed by the next successful roll only and then resets automatically to `0 / None`.

## Certified versus situational maths

Character-derived modifiers remain untouched. A roll may therefore display a breakdown such as:

`14 +3 + situational +2 = 19`

or:

`14 +3 + situational d4 (3) = 20`

This lets DM rulings, temporary magical effects and environmental bonuses remain clearly distinct from the character's authoritative modifier.

## Bonus dice

A situational bonus die is rolled with the same secure RNG used by the Guild Dice Engine and is rendered as an additional highlighted die in the Diceworks stage.

The bonus die contributes only to the arithmetic total. It never changes the natural d20 result used for:

- Natural 20;
- Natural 1;
- critical-hit reactions;
- advantage/disadvantage kept-die selection.

## Dice Ledger

Structured Dice Ledger entries now retain the consumed situational adjustment alongside the normal formula, dice, modifier, total and natural result.

## Quick Rolls and Free Rolls

Quick Rolls use whatever situational adjustment is currently staged when invoked, then clear it after the roll. Saved favourites themselves do not persist situational adjustments.

Guild Free Rolls can also consume the staged next-roll adjustment. Their saved Quick Roll definition remains only the designed quantity, die and base modifier.

## Boundary

Situational adjustments are Diceworks-only play state. They do not modify CharacterRepository, ability scores, proficiency, equipment, spells, progression, Vital Measures or the Living Register.
