# Character Lifecycle Initiative — Phase III.7.2.3
## The Illuminator's Workbench

This pass repairs and polishes the existing portrait customisation
controls without yet attempting the full Great Portrait Expansion.

### Runtime corrections

- Eyes now target `.gmrc-portrait-layer--eyes`.
- Mouths now target `.gmrc-portrait-layer--mouth`.
- Outfit/equipment replacement follows the PHP renderer's
  `data-portrait-asset-slot` contract.
- Existing `data-portrait-asset-use`, new `data-portrait-asset-id`,
  and SVG href identifiers are all understood by the updater.
- A newly generated race/class/name recipe becomes Reset's current
  deterministic baseline.
- Whole-portrait randomisation only touches layers that genuinely have
  more than one available variant.

### Workbench experience

- Rows appear only for genuinely adjustable layers.
- Each row displays its current `n of total` variant position.
- Previous, random and next controls announce the resulting position.
- Portrait collections with no alternate art display a truthful
  "single painted set" notice rather than non-functional arrows.
- The existing custom-image upload workflow is preserved.

Generation 2 currently contains one complete Fructan Grocer painted set.
Its controls intentionally remain locked until the Great Portrait Expansion
adds coherent alternate Generation 2 collections rather than swapping
isolated pieces of a painted composite.
