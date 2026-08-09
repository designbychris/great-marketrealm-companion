# Guild Hall Initiative — Phase II.2: The Grand Entrance

The Companion's cocoa Guild Navigation enters the application like a
physical object rather than appearing as a flat interface element.

## Motion

On each full Companion page load the bar:

1. begins just above the viewport;
2. falls into its normal position;
3. compresses very slightly at impact;
4. rebounds once;
5. performs one smaller settle;
6. becomes completely still.

The complete animation lasts approximately 760ms.

Tablet/mobile uses a shorter travel distance.

`prefers-reduced-motion: reduce` disables the entrance completely.

## Phase II.2.1 refinement

The Companion no longer renders a plugin-owned footer. The surrounding
WordPress installation owns the site's footer and can use its own imagery
without the Companion duplicating that structure.

The cocoa Guild Navigation now uses a dedicated woodgrain background instead.

The application uses a compressed WebP texture at runtime. The original PNG
master remains in the Guild Illustration Kit.
