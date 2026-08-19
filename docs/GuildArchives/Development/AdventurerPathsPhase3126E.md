# Phase III.12.6E — The Paladin's Final Seal

III.12.6E seals the complete Paladin Calling after browser and PHPUnit
certification.

## Final certified stack

The Paladin now has:

- specialist 1–20 Calling progression;
- eight Sacred Oaths;
- automatic Oath Gifts at 3 / 7 / 15 / 20;
- creation-time Oath guidance and previews;
- Lay on Hands;
- Divine Sense;
- Sacred Save DC;
- Aura progression;
- Cleansing Touch;
- persistent Sacred Reserves;
- shared persistent spell slots;
- correct half-caster slot progression;
- variable Lay on Hands;
- named Divine Sense and Cleansing Touch actions;
- spell-slot-backed Divine Smite;
- Guild Diceworks radiant damage rolls;
- long-rest restoration.

## Smite browser containment

Live browser testing exposed that the earlier desktop Smite rule measured the
browser viewport rather than the actual half-width Sacred Action card.

The Final Seal makes each Sacred Action card an inline-size container.

Each Smite option now lays out as:

1. slot information across the full row;
2. Commit Slot;
3. Roll Smite.

The two actions share equal width and are prevented from exceeding the card.

When the Smite option falls below 17rem, the controls stack into one column.

This fixes the overflow without hiding or clipping controls.

## Lay on Hands target boundary

`Heal this Paladin` continues to update this Character's live HP.

`Record spend for another creature` records only the Sacred Reserve spend.
Cross-character mutation remains reserved for a future Fellowship/party target
system.

## Divine Smite boundary

The Companion consumes a real shared spell slot but does not infer that a melee
weapon hit qualifies.

The table confirms the trigger first.

## Final Seal purpose

This phase is primarily an integration and regression seal. It protects the
boundaries established throughout III.12.6A–D rather than adding a new Paladin
subsystem.
