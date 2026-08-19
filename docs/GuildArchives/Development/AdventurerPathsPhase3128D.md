# Phase III.12.8D — The Sorcerer's Metamagic Arts

III.12.8D turns the Sorcerer's Metamagic milestones into persistent character
choices and active-play Sorcery Point expenditure.

## Metamagic allowance

The certified Sorcerer progression remains:

- Level 3 — two Metamagic options
- Level 10 — three Metamagic options
- Level 17 — four Metamagic options

The Ledger requires exactly the current allowance when saving selections.

## Metamagic catalogue

The phase provides eight classic Metamagic techniques:

- Careful Spell — 1 Sorcery Point
- Distant Spell — 1 Sorcery Point
- Empowered Spell — 1 Sorcery Point
- Extended Spell — 1 Sorcery Point
- Heightened Spell — 3 Sorcery Points
- Quickened Spell — 2 Sorcery Points
- Subtle Spell — 1 Sorcery Point
- Twinned Spell — Sorcery Points equal to spell level, minimum 1

Each option includes player-facing summary and timing guidance.

## Persistence boundary

Metamagic selections are durable character choices and are therefore stored
separately from active resource expenditure in owner-scoped character meta:

`_gmrc_sorcerer_metamagic`

Sorcery Point expenditure continues to use the III.12.8C shared active class
resource ledger.

This preserves the distinction between:

- what the Sorcerer knows;
- what the Sorcerer has spent.

## Active play

Every selected Metamagic Art receives an active-use control.

Using an Art:

1. confirms that the Art is one of the character's certified selections;
2. resolves its Sorcery Point cost;
3. spends that cost from the existing Font of Magic reserve;
4. rejects the use if insufficient Sorcery Points remain.

Twinned Spell asks for the spell level at use time because its cost is
variable.

## Phase boundary

III.12.8D records Metamagic expenditure and guidance. It does not attempt to
automatically rewrite the underlying spell's targets, duration, casting time,
components or dice.

Those effects still require table/context confirmation, while the Companion
handles the durable choice and resource accounting correctly.

## Accessibility

Selection uses native checkboxes and labelled controls. Active-use forms are
server-backed and keyboard operable without JavaScript. Layout stacks on
narrow screens and retains forced-colours support.
