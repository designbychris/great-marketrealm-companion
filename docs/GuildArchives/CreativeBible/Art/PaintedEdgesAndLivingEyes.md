# Painted Edges and Living Eyes

## Blink handoff

The staged illumination must release its animation transform after the
final seal lands. Otherwise the reveal retains ownership of the eye
layer's transform and prevents the blink squash from becoming visible.

The Registrar's Desk therefore removes the active reveal class after
2.86 seconds and the living portrait schedules blinking only after the
`data-illumination-ready` state becomes true.

## Painted edges

Selected large silhouettes use a low-strength SVG displacement filter
driven by fractal noise.

The treatment is intentionally restrained:

- displacement scale: 1.25;
- two noise octaves;
- applied only to major silhouettes;
- facial features remain crisp;
- text and small equipment details remain undistorted.

This creates a slight hand-painted wobble without making the portrait
fuzzy or reducing thumbnail readability.
