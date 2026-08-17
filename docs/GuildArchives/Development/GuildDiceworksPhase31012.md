# Phase III.10.12 — The Targeting Contract

Guild Diceworks now has an explicit, target-aware roll contract without mutating target state.

## Stable target kinds

The combat target model defines five stable categories:

- Self
- Ally
- Player Character
- NPC
- Hostile Creature

`RollTarget` separates a target reference from target resolution.

A resolved target has a concrete ID and label. A reference target may carry only a kind and human-readable label.

## Current resolution boundary

The current Character Ledger can resolve **Self** because it owns the open Character record.

Ally, Player Character, NPC and Hostile Creature are currently descriptive references. They deliberately remain unresolved until a later party/encounter target registry exists.

This prevents Diceworks from pretending that a typed label such as “Gravy Golem” is a persistence-safe combat entity.

## Roll targeting policy

Weapon attacks, weapon damage, spell attacks, spell damage and healing rolls declare `target_mode=creature`.

Rolls without recipient semantics remain target-free.

Self-range healing/features can declare Self as their default target. Other creature rolls begin without a selected target.

A roll is still allowed without a target in III.10.12 because this phase records intent only. III.10.13 may require a resolved target before an HP mutation.

## Diceworks UI

Applicable rolls expose a Target panel with:

- target kind;
- optional target name/label for unresolved categories;
- clear resolved/reference-only messaging.

The result displays its target context and The Dice Ledger stores the structured target alongside the formula, dice, modifiers, natural result and situational adjustment.

## Critical inheritance

A critical-damage follow-up inherits the target captured by the original natural-20 attack. Changing the target selector after the attack cannot silently retarget that pending critical result.

## No vitality mutation

This phase does not:

- apply damage;
- apply healing;
- change Current HP;
- change Temporary HP;
- create NPC vitality;
- create encounter state.

The removed untargeted Vital Measures bridge remains removed.

Phase III.10.13 will consume this contract and may apply results only when the selected target is appropriate and resolvable.
