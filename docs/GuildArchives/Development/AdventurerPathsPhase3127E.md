# Phase III.12.7E — The Warlock's Final Seal

III.12.7E certifies the complete Warlock Calling built across III.12.7–D.

## Final certified stack

The Warlock now has:

- specialist Level 1–20 Calling progression;
- Level 1 Otherworldly Patron contract;
- four registered Marketrealm Patrons;
- identity / playstyle / best-for Patron guidance;
- automatic Patron Gifts at Levels 1 / 6 / 10 / 14;
- persistent Pact Magic reserves;
- dedicated Pact slot identity separate from ordinary spell slots;
- short-rest and long-rest Pact restoration;
- level-aware Pact slot level and slot-count progression;
- Eldritch Invocation known-count milestones;
- Pact Boon milestone at Level 3;
- Mystic Arcanum milestones at 6th / 7th / 8th / 9th spell circles;
- Eldritch Master at Level 20;
- active Bureaucratic Hex with independent beam resolution;
- Guild Diceworks attack and force-damage controls for every beam;
- responsive and forced-colours-aware Ledger presentation.

## Bureaucratic Hex browser polish

Live browser testing found that the beam attack button used the literal text
`20` as an `aria-hidden` decoration.

Although this was meant to represent a d20, visually it read as though `20`
were part of the button label.

III.12.7E replaces it with the decorative `✥` attack glyph.

The accessible button text remains simply:

`Roll Beam Attack`

## Independent beam rule

Bureaucratic Hex remains:

- 1 beam at Levels 1–4;
- 2 beams at Levels 5–10;
- 3 beams at Levels 11–16;
- 4 beams at Levels 17–20.

Every beam is its own spell attack and deals `1d10` force on a hit.

The Companion does not combine the beams into a single scaling damage roll.

## Pact Magic boundary

Warlock Pact Magic remains intentionally separate from the shared ordinary
spell-slot ledger.

Pact slots use `pact-magic-slot` and restore on a short or long rest.

## Final Seal boundary

The Final Seal protects the currently implemented Warlock contract.

It does not invent missing source content. In particular, the repository does
not yet provide:

- a selectable Eldritch Invocation catalogue;
- selectable Pact Boon options;
- named Mystic Arcanum choices;
- a numbered Warlock spell catalogue beyond Bureaucratic Hex.

Those can be added as future expansions without weakening the Calling that is
certified here.
