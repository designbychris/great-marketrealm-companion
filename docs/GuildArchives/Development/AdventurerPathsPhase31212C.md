# Phase III.12.12C — The Bard’s College Gifts on the Living Ledger

## Status

Implementation slice prepared for certification.

## Purpose

Phase III.12.12C turns the canonical Bard College Gifts registered in
III.12.12B into a read-only, level-aware Living Ledger record.

The shared `PathGiftCatalogue` remains the mechanics source. The new Bard
College Gift Ledger presenter does not create or persist additional mechanics;
it projects which supplied College Gifts are available at the character’s
current Bard level and which supplied College milestone comes next.

## Certified boundaries

- Foreign Callings do not receive the Bard College Gift Ledger.
- A Bard without a chosen College receives no College Gift mechanics.
- Multiple gifts at the same level are presented together.
- Level 3, 6 and 14 boundaries are derived from the canonical gift catalogue.
- Future gifts remain future-only until their supplied level is reached.
- The College of Nostalgia is complete after its supplied Level 6 feature;
  no Level 14 feature is invented.
- Player-facing cards use the certified gift detail already stored by
  III.12.12B.
- The panel is read-only and does not alter `PathGifts` persistence or
  advancement certification.

## Living Ledger presentation

The Bard’s College Register now contains a “Living College Gifts” section for
the chosen College. It shows:

1. every College Gift available at the Bard’s current level;
2. the full player-facing mechanical detail for each available gift;
3. the next supplied College milestone and all gifts arriving at that level;
4. a completion message when the handbook supplies no later College Gift.

This keeps active-play orientation close to Bardic Inspiration, spellcasting,
and the College identity while preserving the generic certified
“Gifts of the Path” record elsewhere on the Ledger.

## Verification target

The Phase III.12.12C regression suite covers the 2→3, 5→6 and 13→14 level
boundaries, multiple same-level gifts, Nostalgia’s shorter progression,
controller wiring, Ledger markup, and responsive/forced-colour styling.
