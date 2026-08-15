# Phase III.10.4 — The Adventurer's Vital Measures

The Character Ledger now separates certified maximum vitality from mutable play-state hit points.

## Live Adventuring Measures

Players can update:

- Current HP;
- Temporary HP.

Maximum HP is displayed as **Guild certified** and is not submitted by the live-play form.

## Controls

The Ledger provides:

- direct numeric entry for Current HP;
- direct numeric entry for Temporary HP;
- −1 / +1 controls for both values;
- a Quick Amount control;
- Apply Damage;
- Apply Healing;
- Save Adventuring Measures.

Damage consumes Temporary HP before Current HP. Healing is capped at Maximum HP. The JavaScript controls are conveniences only: the form submits the final Current and Temporary values to the server, where they are validated again.

## Domain boundary

`HitPoints::withLiveState()` preserves Maximum HP by construction. `Character::updateVitalMeasures()` exposes that safe mutation to the application layer. The dedicated `/characters/{id}/vital-measures` route persists only the live hit-point state through the existing Character repository.

Current HP must remain between 0 and Maximum HP. Temporary HP must remain between 0 and 999.

This phase does not add entries to progression history or the Living Register. It prepares a clean application boundary for a later Diceworks action such as “Apply 7 damage” or “Apply 5 healing”.
