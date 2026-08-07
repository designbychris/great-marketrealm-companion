# Auby Desk Hero Composition

Phase II.1 introduces the first hero-quality wide Auby Desk scene.

## Composition

The illustrated Guild Hall is no longer treated as a close crop.

The scene remains large enough to show:

- Auby;
- the desk;
- Guild Journal;
- books and records;
- the arched window;
- lantern;
- quill;
- surrounding Guild Hall clutter.

The painting occupies more of the horizontal surface before dissolving into
the deep Guild brown information area.

## Ambient safety

Ambient effects are hidden with `display: none` unless the active scene
explicitly enables them.

The sleeping `Z` effect has an additional contract:

- `data-guild-hall-daypart="late-night"` must be active;
- `data-ambient` must contain `sleep`.

This prevents sleep effects appearing during daytime even if stale state is
present.
