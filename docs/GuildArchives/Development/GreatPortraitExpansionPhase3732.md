# Character Lifecycle Initiative — Phase III.7.3.2
## Project Golden Apple — The Great Portrait Expansion

This phase expands the race/heritage side of the portrait library while
preserving class wardrobe as an independent concern for Phase III.7.3.3.

### Catalogue coverage

- 14 current Grand Catalogue race families receive dedicated body art.
- 44 handbook heritages receive explicit visual overlays.
- Each race family receives two body-form variants for the Workbench.
- Legacy Melonian, Stalker and Meatkin top-level records retain compatible
  expanded body art.

### Important contract correction

`heritage` in the Grand Catalogue is now distinct from the persisted
`portrait_heritage` asset field.

A selection such as:

`Fructan -> Applekin`

resolves to:

`fructan-heritage-applekin`

and that concrete SVG asset is what is saved in the portrait recipe.

### Layer independence

Race anatomy and heritage render before face and class layers:

1. race body;
2. catalogue heritage overlay;
3. eyes and expression;
4. class wardrobe/equipment;
5. accessories/effects/frame.

This keeps race + class combinations reusable for the Guild Wardrobe phase.

### Dressing Table refinement

The customiser gets a wider label column, slightly smaller controls/counters,
and a brass divider before the global Randomise / Reset actions.
