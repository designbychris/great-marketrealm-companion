# Character Creation Card Artwork

Character Creation Race, Class and Background choice cards may now display a Steward-selected WordPress Media Library image.

The Steward's Office exposes **Character Card Artwork**, grouped into Folk & Races, Classes & Callings, and Backgrounds. Artwork is keyed by the same stable identifiers used by Character Creation, so the presentation does not alter mechanics or persisted Character identity.

If no image is assigned, the existing initial/monogram remains the automatic fallback. Assigned images are centre-cropped into a taller artwork panel and receive a subtle zoom when the card is hovered or selected. Reduced-motion users receive the static image without the zoom transition.


## Layout polish

Illustrated cards use an edge-to-edge centre crop with a taller 15.5rem desktop
artwork stage. Explicit image resets protect the cards from theme/global image
padding and max-width rules.

The Steward preview intentionally uses a compact 4:3 media frame rather than the
larger torn-paper artwork treatment used elsewhere in canonical content editing.
This keeps the Media Library controls and explanatory copy readable beside the
preview.
