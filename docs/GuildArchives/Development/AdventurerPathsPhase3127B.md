# Phase III.12.7B — The Warlock's Patron Gifts

III.12.7B promotes the four registered Warlock Patrons into certified shared
Path Gift progressions.

## Patron Gift cadence

Each Patron now has four automatic Gifts:

- Level 1 — the defining first clause of the Patron bargain
- Level 6 — the first major maturation
- Level 10 — deeper Patron authority
- Level 14 — the final Patron feature

This follows the Warlock Patron progression family already reserved by
III.12.7.

Because the Character is created at Level 1, the first gift is safely caught up
by the existing `PathGiftFolio` and `GuildCertificationService` during the
Warlock's first advancement if it is not already present in the Guild Record.

## Pact of the Mascot

- 1 — Smiling Sponsorship
- 6 — Brand Ambassador
- 10 — Impossible Endorsement
- 14 — Mascot Unmasked

## The Forgotten Freezer

- 1 — Frostbound Whisper
- 6 — Cold Storage
- 10 — Door Left Open
- 14 — Heart of the Forgotten Freezer

## The Spoilfather

- 1 — First Bloom of Rot
- 6 — Patient Decay
- 10 — Feast of Spoilage
- 14 — Spoilfather’s Heir

## The Sugar Fiend

- 1 — First Taste
- 6 — Sugar Rush Bargain
- 10 — Glazed Temptation
- 14 — Sweetest Ruin

## Shared infrastructure

The gifts are registered through `PathGiftCatalogue`.

They therefore feed the existing:

- creation-time subclass / Patron preview;
- `PathGiftFolio`;
- Guild certification;
- Guild Record Path Gift presenter;
- Patron Contract Register.

The Register displays only gifts already certified on the Character. Future
gifts are previewed but are not presented as owned.

## Mechanical boundary

III.12.7B establishes Patron identity, gift names, narrative mechanics and
progression timing.

It does not yet implement:

- Pact slot spending;
- short-rest Pact slot recovery;
- Invocation choices;
- Pact Boon choices;
- Mystic Arcanum selection or expenditure;
- active Patron-specific buttons.

Those remain later Warlock slices.
