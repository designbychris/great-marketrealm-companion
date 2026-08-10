# Character Lifecycle Initiative — Phase III.1.2: The Ledger Restoration Pass

This pass closes the remaining visual issues discovered after Complete
Registration became fully functional.

## Living Portrait

Generation 2 no longer animates each face/body SVG `<use>` independently.

All character paint layers — including open eyes and painted eyelids — are
placed inside one `gmrc-g2-breathing-group`. Breathing is applied once to that
parent group. Blink continues to swap eye paint using opacity.

This removes transform drift between the eyes and the Apple Fructan face in the
Open Ledger.

## Wax Seal

The Guild seal is now anchored to the lower-right parchment area of the identity
page rather than floating beside the portrait.

## Auby Desk

Four distinct approved scene compositions remain:

- Morning;
- Day / Afternoon;
- Evening;
- Late Night.

For this restoration pass the runtime masters are rebuilt at 3200 × 1980 with
a restrained two-stage detail recovery pass. Dawn continues to alias Morning;
Night continues to alias Late Night so the six-daypart schedule stays intact.
