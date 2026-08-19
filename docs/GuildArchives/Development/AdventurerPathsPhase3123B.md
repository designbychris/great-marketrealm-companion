# Phase III.12.3B — The Barbarian's Primal Paths

III.12.3B gives all eight registered Barbarian Primal Paths a real Gifts of
the Path progression and improves specialist choice information before a
player commits to a Path.

## Primal Path gift cadence

Every Barbarian Path now grants automatic gifts at:

- Level 3
- Level 6
- Level 10
- Level 14

The first Level 3 gift can be discovered and Guild Certified in the same
advancement that records the chosen Primal Path.

## Path of the Butchered Rage benchmark

The live benchmark Path now progresses through:

- Level 3 — Bloodied Cleaver
- Level 6 — Butcher's Instinct
- Level 10 — Carving Frenzy
- Level 14 — Slaughterhouse Fury

Its identity is aggressive close-quarters pressure with strong Butcher Isles
flavour.

## The other seven Paths

All other registered Barbarian Paths receive the same complete four-stage
progression:

- Path of the Great Tony
- Path of the Expired
- Path of the Marbled Rage
- Path of the Rind
- Path of the Sugarrush
- Path of the Pickled Rage
- Path of the Butterbound

## Better choices before certification

The Path selection UI now supports reusable specialist-choice guidance.

For Paths with enriched guidance, a player can see:

- Path identity;
- playstyle;
- who the Path is likely to suit;
- a preview of its future Path Gifts.

This information is presented before the radio-button choice is recorded.

The infrastructure lives in the shared Path candidate layer and advancement
view rather than in a Barbarian-only template, so Fighter, Wizard and future
Callings can use the same richer pattern as their content is expanded.

## Certified gifts in the Rage Register

The Barbarian Rage Register now displays certified Primal Path Gifts.

Future gifts remain hidden until they have actually been Guild Certified.

## Persistence

No new persistence format is introduced.

Barbarian Path Gifts continue to use the shared Character `PathGifts` value
object and `_gmrc_path_gifts` persistence already proven by Wizard and Fighter.

## Next slice

III.12.3C — Rage Reserves can now focus entirely on active Rage use and state:

- remaining Rages;
- Enter Rage;
- End Rage;
- active Rage state;
- long-rest restoration;
- Level 20 unlimited Rage.
