# Guild Hall Initiative — Phase IV: The Living Guild

## Principle

> One delightful thing at a time.

The Guild Hall should feel inhabited, never busy.

Phase IV adds a shared environmental layer to Auby's Desk. After the room has
settled, a single optional story beat may occur.

Initial story beats:

- a copper coin catches the light;
- Auby's quill nudges;
- a Journal page flutters;
- a tiny Market Mouse peeks out;
- a Purple Thumbprint quietly appears.

## Copper coin

The first interactive story beat is deliberately non-economic.

Picking up the coin displays a small live-region message and records the
discovery for the browser session. It does **not** modify character gold yet.

Project Leather Satchel can later connect this interaction to real inventory
or currency through an explicit domain action.

## Seasons

The controller exposes `data-guild-season` with four broad states:

- spring;
- summer;
- harvest;
- winter.

Phase IV uses only very subtle colour hooks. Dedicated seasonal illustrations
can build on the same contract later.

## Accessibility

Decorative story beats are skipped when reduced motion is requested.

The copper coin remains keyboard-focusable only while its story beat is
active and uses a readable button label.
