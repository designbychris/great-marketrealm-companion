# Phase III.10.1 — The Guild Dice Engine

Phase III.10 begins by formalising the existing Open Ledger roll tray as the Guild Diceworks engine rather than replacing the earlier Guild Dice foundation.

## Engine contract

The Diceworks continues to support:

- d20 checks, saving throws, initiative, attacks and spell attacks;
- Normal, Advantage and Disadvantage modes;
- damage and healing formula rolls;
- secure browser randomness with rejection sampling when Web Crypto is available;
- recent-roll history;
- focus return and Escape-to-close behaviour;
- polite ARIA live announcements of completed roll arithmetic.

Natural d20 results are classified independently from the final modified total. This keeps presentation effects from altering the rules result.

## Natural 20 — Guild celebration

A selected natural 20 receives a Guild celebration state. The result tray creates a short confetti burst and shows a textual `Natural 20` banner. Attack rolls retain their Critical Hit wording.

The visual celebration is decorative only. It never changes the natural roll, modifier or total.

## Natural 1 — the lonely confetti protocol

A selected natural 1 receives a deliberately disappointing Guild response:

- a textual `Natural 1 — Oh dear.` banner;
- Auby records: `The Guild has elected not to record that one.`;
- exactly one decorative confetti element is created and allowed to drift sadly through the tray.

This is intentionally presentation-only and does not impose an automatic-failure rule on roll types where the game rules do not do so.

## Accessibility and motion

Natural 20 and Natural 1 are included in the textual recent-roll/live-region result. Critical meaning therefore never depends on animation or colour.

Under `prefers-reduced-motion: reduce`, confetti animation is suppressed while the textual natural-roll banner remains available.

## Next steps

Later Guild Diceworks phases can build on this trusted result contract for richer dice presentation, formula handling, character-aware actions, roll memory and the live Adventuring Measures HP workflow.
