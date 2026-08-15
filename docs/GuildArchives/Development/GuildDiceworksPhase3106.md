# Phase III.10.6 — Diceworks Meets Vital Measures

This phase established the architectural boundary between Guild Diceworks and the Adventurer's Vital Measures.

The first implementation proved that a semantic dice result could be handed to the live HP subsystem. During browser testing, however, we identified an important targeting rule: an attack or healing roll does not necessarily target the player character whose Ledger produced the roll.

## Preserved architecture

The useful boundary remains:

- Diceworks produces contextual damage/healing results.
- Vital Measures owns player-character Current HP and Temporary HP.
- Character/HitPoints domain rules remain authoritative for damage and healing.

## Targeting correction

Phase III.10.6.1 removes the untargeted `Apply Damage` / `Apply Healing` action from Diceworks.

A future combat-target system must explicitly identify the recipient before a roll result can mutate HP. Valid future targets may include:

- self;
- another player character;
- an allied NPC;
- a hostile NPC or monster controlled by the DM.

Until that target exists, Diceworks remains result-only.

Manual Adventuring Measures continue to support Current HP and Temporary HP changes for the player character.
