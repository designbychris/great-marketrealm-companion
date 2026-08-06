# The Generation 2 Portrait Library

> **Keeper Auby's note:** Build every portrait as though its owner might step
> into the marketplace and join us for tea.

## Purpose

Generation 2 defines the asset pipeline for richer, Auby-inspired portraits.
It replaces hard-coded PHP asset mappings with discoverable, self-describing
manifest files while retaining the current deterministic recipe system.

## Design goals

- Rich illustrated SVG artwork with layered shading and highlights.
- Independent race and class construction.
- Automatic asset discovery.
- Deterministic defaults and safe fallbacks.
- Editor-friendly folders and filenames.
- Strict validation before an asset reaches the renderer.
- Backwards compatibility with Generation 1 recipes.

## Architecture

```text
Generation2/
├── manifest.schema.json
├── Shared/
│   ├── Faces/manifest.json
│   ├── Backgrounds/manifest.json
│   ├── Frames/manifest.json
│   └── Effects/manifest.json
├── Races/
│   └── Fructan/
│       ├── manifest.json
│       └── Assets/
├── Classes/
│   └── Grocer/
│       ├── manifest.json
│       └── Assets/
└── Collections/
    └── FructanGrocer/
        └── manifest.json
```

Each manifest owns one coherent family of assets. The PHP catalogue discovers
all `manifest.json` files recursively and exposes their assets without requiring
a PHP mapping update.

## Asset identity

Every asset has a stable recipe ID:

```text
g2-fructan-body-apple-01
g2-grocer-outfit-everyday-01
g2-eyes-auby-bright-01
```

IDs never contain file paths. Files may move during development while IDs remain
stable for saved characters.

## Layer contract

Generation 2 uses the shared 480 × 600 canvas and these logical slots:

1. background
2. ground_shadow
3. body_base
4. body_shadow
5. body_highlight
6. heritage
7. eyes
8. mouth
9. outfit_base
10. outfit_shadow
11. outfit_highlight
12. equipment
13. accessory
14. class_effects
15. ambient_effects
16. guild_ornament
17. frame

A manifest may group several visual sublayers into one SVG asset, but it must
declare the slot it occupies.

## Discovery flow

```text
PortraitManifestRepository
        ↓
recursive manifest discovery
        ↓
PortraitManifestValidator
        ↓
PortraitAssetCatalogue
        ↓
variant registry / renderer
```

Invalid manifests are excluded and reported rather than partially loaded.

## Backwards compatibility

Generation 1 remains the fallback. A recipe can choose a Generation 2 asset ID
when available; otherwise the existing procedural or Generation 1 asset is used.

## Related volumes

- [Portrait Engine](PortraitEngine.md)
- [The Illuminated Art Style](../CreativeBible/Art/Generation2PortraitStyle.md)
- [Generation 2 SVG Standards](../CreativeBible/Art/Generation2SVGStandards.md)
