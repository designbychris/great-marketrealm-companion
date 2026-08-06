# Generation 2 SVG Standards

## Canvas

Every asset uses:

```svg
viewBox="0 0 480 600"
```

## Required rules

- Static SVG geometry only.
- No scripts or event handlers.
- No embedded raster images.
- No external URLs or references.
- No `foreignObject`.
- No embedded fonts.
- IDs must be unique within the file.
- All logical groups need descriptive IDs.
- Transparent background except for background assets.
- Top-left lighting.
- Coloured local outlines.

## Anchor zones

```text
head centre:      x 240, y 215
eye line:         y 220
mouth line:       y 266
shoulder line:    y 352
hand zones:       x 115 and x 365, y 420–505
ground line:      y 545
```

Artists may vary silhouettes, but class outfits must respect the shoulder,
torso and equipment anchor zones so they remain race-independent.

## File layout

```text
Assets/
├── body-base.svg
├── body-shadow.svg
├── body-highlight.svg
├── heritage-leaves.svg
└── ...
```

## Export checklist

1. Convert unsupported effects to paths or gradients.
2. Remove editor metadata where practical.
3. Confirm the 480 × 600 viewBox.
4. Run the manifest validator.
5. Inspect at full portrait and 96-pixel thumbnail sizes.
6. Test over at least three compatible race/class combinations.
