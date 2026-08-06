# Generation 2 Portrait Asset Workflow

## Adding a new asset

1. Choose the owning manifest.
2. Export an SVG using the 480 × 600 canvas.
3. Give the asset a stable `g2-` recipe ID.
4. Add its manifest entry.
5. Run the manifest tests and validator.
6. Inspect the portrait at full and thumbnail sizes.
7. Test it with unrelated compatible race or class layers.
8. Update the relevant Art Bible volume.

## Naming

Recipe IDs:

```text
g2-{owner}-{slot}-{variant}-{number}
```

File names are concise and local to their folder:

```text
Assets/apple/base.svg
Assets/apple/shadow.svg
Assets/apple/highlight.svg
```

## Pull-request checklist

- Manifest validates.
- Asset path exists.
- SVG passes safety checks.
- No duplicate asset ID exists.
- Declared slot is supported.
- Default asset exists.
- At least one PHPUnit test covers new discovery behaviour.
