# Auby Desk Scene Library

Phase II replaces the simplified desk SVGs with a manifest-backed raster scene library.

## Initial scenes

```text
05:00–07:59  dawn
08:00–11:59  morning
12:00–16:59  afternoon
17:00–20:59  evening
21:00–23:59  night
00:00–04:59  late-night
```

Each scene carries a daypart, mood, activity, image, ambient effects and contextual status text. Multiple scenes may later share a daypart, allowing variants such as `afternoon-biscuit`, `winter-evening` or `late-night-buried-in-scrolls` without changing the component markup.
