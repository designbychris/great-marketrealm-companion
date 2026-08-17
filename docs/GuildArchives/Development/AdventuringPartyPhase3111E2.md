# Phase III.11.1E.2 — Exorcising the Fellowship Portraits

The first browser pass of the Fellowship Register revealed the familiar
static-portrait ghost state.

## Cause

Fellowship views correctly reused the authoritative `PortraitRenderer`, but
their raw SVG output did not receive the completed static paint-state rules
already established for the Character Register.

The portrait therefore inherited the base Portrait Studio reveal CSS:

- race/class layers could begin desaturated or transparent;
- effects could remain in their pre-reveal state;
- living portrait motion remained eligible to start.

The Character data and portrait recipe were correct. The problem was purely
presentation state.

## Static Fellowship paint state

Both Fellowship surfaces now force the finished painted state:

- company portrait;
- individual Fellowship roster portrait.

Race, class and effect layers are fully opaque and saturated, reveal
transitions are disabled, and living portrait motion is disabled on these
static display surfaces.

The live Character Ledger and Portrait Studio are unchanged.

## Company portrait composition

The group portrait now behaves as one assembled illustration instead of a
stack of individual framed records.

For generated portraits the composition suppresses:

- standard portrait background layer;
- standard frame layer;
- Generation 2 background artwork;
- Generation 2 frame artwork.

The Fellowship component's own per-member border/background/shadow is also
removed. The outer Fellowship portrait supplies the shared company backdrop.

This allows the adventurer artwork itself to overlap into one visual company.

## Custom media limitation

A custom uploaded portrait is a raster image. If its background or frame is
already baked into the uploaded pixels, CSS cannot safely remove it.

Custom portraits still receive the borderless company composition treatment,
but their authored image pixels remain intact.

## Architectural boundary

No changes are made to `PortraitRenderer`, portrait recipes, persistence,
Portrait Studio, Character Ledger rendering or living portrait logic.

The exorcism is scoped entirely to Fellowship presentation.
