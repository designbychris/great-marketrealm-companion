# Phase III.12.10A — The Druid's Circle Grove Register

III.12.10A adds a Druid-specific read-only Circle Grove Register to the
Spells & Abilities Ledger.

## Level 1 first

The Register is deliberately useful before Wild Shape or Circle selection
exists.

A Level 1 Druid sees:

- prepared spellcasting already active;
- Wisdom spell save DC;
- Wisdom spell attack bonus;
- prepared-spell maximum;
- two baseline cantrips;
- first-circle shared spell slots;
- Wild Shape shown as opening at Level 2;
- Circle selection shown as opening at Level 2;
- the next Druid milestone.

This makes a newly created Level 1 Druid testable without pretending that
Level 2 features are already unlocked.

## Prepared spellcasting

The Register calculates the current preparation maximum as:

`Druid level + Wisdom modifier`, minimum one.

It does not treat prepared spells as permanent known-spell progression.

## Wild Shape

III.12.10A displays a transformation stage only:

- Stage 0 before Level 2;
- Stage 1 from Level 2;
- Stage 2 from Level 4;
- Stage 3 from Level 8.

No Wild Shape use counter is created in this phase because the current
Marketrealm project source does not yet provide a Druid-specific active-play
resource contract for it.

## Circle Grove

The six already bundled Druid Circles are visible in the Register.

Before Level 2, the Register says that Circle choice opens at Level 2.

Once a Circle is certified, its catalogue label is displayed.

Circle Gifts remain explicitly unavailable until their dedicated phase rather
than being fabricated from Circle names alone.

## Shared spell slots

The Grove Register reads from the same `ActiveClassResourceState` and
`SharedSpellSlotReserveService` used by the rest of the Companion.

It therefore shows current remaining / maximum spell-slot state without
creating a duplicate Druid slot ledger.

## Presentation

The Grove Register keeps the Companion parchment language but adds a subtle
organic treatment through softer green-tinted surfaces and a decorative leaf
mark.

It remains:

- responsive;
- keyboard-neutral because it is read-only;
- reduced-motion safe;
- forced-colours safe;
- usable on narrow mobile layouts.
