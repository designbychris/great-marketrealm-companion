# Character Lifecycle Initiative — Phase III.1.3: The Living Ledger

## Root cause of the persisted portrait bug

The Character Creator builds Generation 2 portrait layers in JavaScript because
it has a live form. Open Ledger renders a persisted SVG on the server and has no
form. The Generation 2 JavaScript therefore exited before constructing the live
classes and the PHP renderer emitted unclassed `<use>` elements.

The Ledger portrait now receives the same class and grouping contract directly
from `Generation2PortraitRenderer`. Persisted portraits are marked illumination
ready immediately by `living-portrait.js`, so breathing and blinking work without
a Creator form.

## Auby approval

The old `g2-auby-finishing-touch-01` cameo has been retired from the active
Fructan Grocer collection. Its source file remains archived for history, but it
is no longer rendered. Persisted portraits use the physical Auby Seal of
Approval in static approved state.

## Ledger navigation

The physical book now has three bottom tabs:

- Overview;
- Skills & Training;
- Archive Notes.

Tabs use the ARIA tabs pattern, Left/Right/Home/End keyboard navigation and
reduced-motion-safe page settling. Equipment, attacks, spells and Guild Dice can
extend the same panel architecture in later phases.
