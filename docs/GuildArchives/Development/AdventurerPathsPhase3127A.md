# Phase III.12.7A — The Warlock's Patron Contract Register

III.12.7A adds the Warlock's read-only Patron Contract Register.

## Patron Contract Register

The Spells & Abilities Ledger now exposes:

- chosen Patron identity;
- current Pact Magic slot level;
- current Pact Magic slot count;
- Pact Magic refresh cadence;
- Eldritch Invocations known;
- Pact Boon availability;
- Mystic Arcanum circles unlocked;
- Charisma-based Pact save DC;
- Charisma-based Pact spell attack;
- next major contract milestone.

## Four current Patrons

The repository currently defines four Warlock Patrons:

- Pact of the Mascot
- The Forgotten Freezer
- The Spoilfather
- The Sugar Fiend

III.12.7A adds identity, playstyle and best-for guidance for all four through the
same shared `PathChoiceGuideCatalogue` used by creation-time subclass previews.

## Read-only boundary

This phase does not yet spend Pact slots or implement Invocation selections.

It also does not invent Patron Gift mechanics.

Those remain assigned to later Warlock slices:

- III.12.7B — Patron Gifts
- later Pact Magic / Invocation reserve and action phases

## Pact Magic accuracy

The register explicitly records the Warlock slot count reaching four at Level
17. This is kept in `WarlockPatronPolicy` so later Pact Magic persistence can
reuse one class-owned authority rather than copying thresholds into the UI.

## Accessibility

The register uses an explicit heading relationship and responsive layout, with
forced-colours support.
