# Phase III.7.3.4.1 — The Living Illuminator
## Living Effects Engine

The Living Illuminator now derives restrained ambient portrait behaviours from
an adventurer's existing race and class identity.

These behaviours are deliberately **not portrait recipe slots**. The saved
portrait remains deterministic artwork; the Living Effects Engine is a
presentation layer that can evolve independently.

### Race-led examples

- Fructan / Vegfolk / Herbfolk — botanical motes
- Fungifolk — drifting spores
- Drinkfolk — rising bubbles
- Frostreem — frost motes
- Sweetfolk / Fluffling / Marshmallow Folk — sugar sparkle
- Recalled — an occasional restrained recall flicker

### Class-led examples

- Wizard / Sorcerer — arcane glimmer
- Druid — nature motes
- Cleric / Paladin — sacred glint
- Warlock — eldritch wisps
- Artificer — tiny workshop sparks

Legacy Grocer and Cleaver Saint characters retain ambient personality without
being restored to the current class catalogue.

### Determinism

Particle placement, timing and scale are derived from the portrait seed. No
`Math.random()` calls are used, so the same adventurer has the same ambient
rhythm after reload.

### Accessibility and lifecycle

- Custom uploaded portraits do not receive generated ambient overlays.
- `prefers-reduced-motion: reduce` removes the overlay entirely.
- Animations pause while the browser tab is hidden.
- Race/class changes re-evaluate the registry without changing persisted
  recipe data.
