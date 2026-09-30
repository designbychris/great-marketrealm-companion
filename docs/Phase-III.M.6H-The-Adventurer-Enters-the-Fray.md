# Phase III.M.6H — The Adventurer Enters the Fray

## Purpose
Bring the existing Companion attack and equipment projections into the native Pocket without creating a second combat rules engine or native inventory store.

## Native Combat
The Combat tab now presents equipped weapon attacks from the Pocket API. Attack bonus, ability, damage formula, damage modifier, damage type, range, properties, and critical damage formula remain server-resolved by the existing `AttackPresenter`.

Attack rolls use the shared native Guild Diceworks and therefore retain Normal, Advantage, Disadvantage, six-entry in-memory history, Natural 20 celebration, and Natural 1 lonely confetti. Damage rolls use the weapon formula supplied by the server. Critical damage uses the server-supplied critical formula so only weapon dice are doubled; the flat modifier is applied once.

## Native Equipment
The More tab now shows the authoritative inventory projection as a read-only Adventurer's Pack. Quantity, equipped state, category, and total weight are presentation only. Equipment changes remain a Companion responsibility.

## Boundaries
- No native character database.
- No inventory mutations.
- No duplicated attack-bonus or critical-damage rules.
- No persistent roll history.
- Spellbook remains deferred to its native phase.
