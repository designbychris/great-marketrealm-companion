# Phase III.12.6D — The Paladin's Sacred Actions

III.12.6D turns the Paladin's persistent reserves into named active-play
actions and establishes a shared spell-slot expenditure ledger.

## Shared spell slots

The Arcane Pantry previously certified spell-slot maximums but did not persist
slot expenditure.

`SharedSpellSlotReserveService` now stores expended slots in the existing
`ActiveClassResourceState` under keys such as:

- `spell-slot-1`
- `spell-slot-2`

This service is deliberately not Paladin-specific. Future Wizard, Warlock,
Sorcerer, Ranger, Druid, Cleric, Bard and Artificer active spellcasting can
reuse the same slot ledger.

The Arcane Pantry now displays `remaining / total` for each spell-slot level.

## Lay on Hands

Lay on Hands becomes a variable Sacred Action.

The player chooses the number of points to spend and whether the recipient is:

- this Paladin; or
- another creature at the table.

When healing this Paladin, the Character's live hit points are restored through
the existing `Character::heal()` domain method.

When healing another creature, the Companion records only the pool spend
because that external creature is not owned by this Character Ledger.

## Divine Sense

Divine Sense is now a named activation rather than a generic reserve-test
button.

Using it consumes one persistent Divine Sense use.

The Companion records activation but does not invent scene information.

## Cleansing Touch

At Level 14, Cleansing Touch becomes a named action and consumes one certified
use.

The table remains responsible for confirming that the magical effect qualifies.

## Divine Smite

At Level 2, Divine Smite presents one option for each currently available
Paladin spell-slot level.

The player commits a real shared spell slot only after the table confirms the
triggering melee weapon hit qualifies.

The displayed radiant damage is:

- Level 1 slot — 2d8
- Level 2 slot — 3d8
- Level 3 slot — 4d8
- Level 4+ slot — 5d8 maximum

The damage roll reuses Guild Diceworks.

Target-dependent bonus dice are not inferred automatically.

## Long rest

The Paladin Long Rest now restores:

- Lay on Hands;
- Divine Sense;
- Cleansing Touch;
- shared spell slots.

Unrelated class-resource state remains untouched.

## Security

Sacred Actions use the existing `gmrc_app_request` bridge and the
`gmrc_character_sacred_{id}` nonce family.
