# Guild Hall Initiative — Phase IV.2: The Guild Welcome

Phase IV.2 brings the Dashboard introduction into the same visual language as
Auby's Desk.

## Welcome treatment

The previous plain heading block becomes a centred parchment notice with:

- warm parchment;
- subtle paper fibres;
- purple tape;
- Guild-purple and gold eyebrow;
- handwritten welcome title;
- decorative Guild divider;
- centred handwritten supporting copy;
- a small Auby heart mark.

All copy remains real HTML rather than being baked into an image.

## Navigation icon correction

Navigation icons have always been stored as inline SVG in `Icons.php`.

The horizontal navigation component previously passed those SVG strings
through `wp_kses_post()`. WordPress strips SVG elements from that allow-list,
which left the icon containers visually empty.

Phase IV.2 uses an intentionally narrow `wp_kses()` SVG allow-list local to
the navigation view. This keeps the current zero-dependency icon architecture
while permitting only the SVG elements and attributes used by the Companion.
