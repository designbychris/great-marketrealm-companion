# Phase III.10.10 — Critical Damage Workflow

A natural 20 on a weapon attack now turns Auby's existing critical-hit guidance into an actionable Diceworks follow-up.

## PHP-authoritative critical formula

`AttackPresenter` now resolves a `critical_damage_die` alongside the normal weapon damage formula.

Only the weapon dice count is doubled:

- `1d8` → `2d8`
- `2d6` → `4d6`

The flat ability modifier remains separate and is therefore added once.

The resolved critical formula is passed to the attack trigger as structured roll context. JavaScript does not infer or rewrite the weapon formula.

## Natural 20 follow-up

When the kept d20 result is a natural 20 and the roll kind is `attack`, Diceworks reveals:

`Roll Critical Damage — <formula> <modifier>`

Auby's existing line remains:

> “Critical hit! Double the weapon dice!”

Natural 20s on checks, saves, spell attacks or free rolls do not invent a weapon critical-damage action.

## Critical damage roll

The follow-up rolls the PHP-resolved critical formula through the shared secure Diceworks formula engine.

It:

- renders the doubled weapon dice;
- adds the flat damage modifier once;
- can consume a newly staged next-roll Situational Adjustment;
- records a distinct `critical-damage` entry in The Dice Ledger;
- clears the follow-up after use.

## Target boundary

Critical damage remains a roll result only. Phase III.10.10 does not mutate Current HP, Temporary HP, NPC vitality, or any target state.

Target selection remains Phase III.10.12 and target-aware HP application remains Phase III.10.13.
