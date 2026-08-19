# Phase III.12.6D — Sacred Actions Browser Hardening

This patch responds to live browser verification of the Paladin Sacred Actions
surface.

## Guild Diceworks trigger hardening

Guild Diceworks previously attached a click listener to every roll trigger found
during initialisation.

The Ledger now uses one delegated click listener at the `data-living-ledger`
boundary and resolves the clicked `data-guild-roll` button through `closest()`.

This keeps existing Attack, Damage, Rogue, Monk and Paladin rolls on one
contract while making class-specific tab-panel rolls resilient to rendering and
future UI changes.

The Paladin Divine Smite button continues to use the standard Diceworks
attributes:

- `data-guild-roll="damage"`
- `data-roll-kind="damage"`
- `data-roll-source="Divine Smite"`
- `data-roll-formula`
- `data-roll-damage-type="radiant"`

## Smite layout

Each spell-slot Smite option is now a self-contained responsive row.

Desktop layouts reserve distinct space for:

- slot level / damage formula / remaining slots;
- Commit Slot;
- Roll Smite.

Narrow screens stack those controls vertically instead of allowing labels and
buttons to collide.

## Lay on Hands recipient boundary

III.12.6D keeps two explicit modes:

- Heal this Paladin — updates this Ledger's live hit points.
- Record spend for another creature — spends the Paladin pool without
  pretending the external target is a linked Character.

Cross-character healing remains a future Fellowship/party integration rather
than silently coupling character records here.
