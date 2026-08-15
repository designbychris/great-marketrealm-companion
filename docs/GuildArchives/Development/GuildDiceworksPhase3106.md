# Phase III.10.6 — Diceworks Meets Vital Measures

Guild Diceworks can now offer an explicit action for a rolled damage or healing result and commit that result through the Adventurer's Vital Measures boundary.

## Player flow

A damage or healing formula roll may produce a contextual action such as:

- `Apply 7 Damage`
- `Apply 5 Healing`

Rolling does **not** change hit points. The player must choose the Apply action deliberately.

D20 checks, saving throws, attacks, spell attacks and Guild Free Rolls remain result-only and do not expose a Vital Measures action.

## Application boundary

Diceworks does not calculate hit-point state. It dispatches a semantic `gmrc:vital-apply` request containing:

- action (`damage` or `healing`);
- rolled amount;
- source;
- originating Ledger tab.

Vital Measures owns the form submission. The server-side Character controller recognises Diceworks commits and delegates to:

- `Character::takeDamage()` for damage;
- `Character::heal()` for healing.

The `HitPoints` domain therefore remains authoritative for temporary-HP absorption, zero-floor damage and maximum-HP healing caps.

## Feedback

After persistence, the Guild flash message reports the applied result. Damage reports Temporary HP and Current HP before/after values; healing reports Current HP before/after values.

The redirect returns to the Ledger tab from which the roll originated.

## Boundary

Maximum HP remains untouched. Diceworks does not write progression or Living Register history and does not automatically apply any roll.
