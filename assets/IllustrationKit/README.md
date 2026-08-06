# The Guild Illustration Kit

The Illustration Kit is the artist-facing source library for Generation 2
portrait artwork.

## First benchmark

The first complete benchmark is the **Apple Fructan Grocer**. Its runtime-ready
copies are also placed under:

```text
app/Modules/Characters/Portraits/Library/Generation2/
```

## Rules

- Shared 480 × 600 canvas.
- Top-left lighting.
- Local-colour outlines.
- Rounded silhouettes.
- Separate base, shadow and highlight passes.
- Static safe SVG only.
- Inspect at portrait and thumbnail sizes.

## Structure

```text
IllustrationKit/
├── Templates/
├── RuntimeMirror/
├── Materials/
├── Shared/
├── Bodies/
├── Clothing/
├── Equipment/
└── Previews/
```

`RuntimeMirror` reproduces the exact manifest paths used by the application.
