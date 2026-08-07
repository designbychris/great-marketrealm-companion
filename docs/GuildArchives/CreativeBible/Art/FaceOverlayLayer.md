# Face Overlay Layer

## Purpose

`face_overlay` is the Generation 2 slot for temporary or decorative facial
artwork that must be painted after the base face.

Examples include:

- closed eyelids;
- tears;
- spectacles shine;
- face paint;
- sweat drops;
- magical eye glow;
- temporary expression flourishes.

## Paint-order contract

The Generation 2 renderer paints:

```text
eyes
mouth
face_overlay
```

in that order.

SVG uses paint order rather than normal HTML `z-index`, so the closed eyelids
must physically be rendered after the open eyes.

## Golden Apple III.1

The Apple Fructan's painted eyelids are the first `face_overlay` asset.

This pass also rounds the Apple silhouette and aligns the Grocer ledger with
the left hand so the portrait reads as one composed illustration.
