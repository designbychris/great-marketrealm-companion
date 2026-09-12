# Interface Translation Audit

This audit separates translatable application interface copy from canonical MarketRealm authored content.

## Translation-ready in this pass

- Companion shell navigation and Guild Hall accessibility labels.
- Character creation and edit workflow labels, choices, review states and guidance.
- Live Adventuring Sheet labels, measures, controls and accessibility text.
- Printable/PDF sheet labels and print-title strings.
- Core Character Ledger navigation, vitals, training, inventory and attack controls.
- Browser-generated character registration, Dice of Destiny, Guild Dice, live-sheet and printable-sheet status copy is supplied from PHP through `wp_localize_script()`.

## Deliberately not treated as generic interface

Race, class, subclass, spell, item and feature names/descriptions remain canonical MarketRealm content. Auby quotations and longer authored lore may also be curated in the content-translation pipeline rather than mechanically translated.

## Rule for new work

New PHP interface strings must use the `great-marketrealm-companion` text domain. JavaScript-visible language must be supplied from PHP localisation data (or a future `wp.i18n` catalogue), rather than adding new hard-coded English messages.
