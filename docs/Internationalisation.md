# Internationalisation & Language Packs

## Principle

The Companion is authored in English but must not be English-only. The application should be able to accept any WordPress locale pack without introducing locale-specific branches into the PHP domain model.

## Two translation layers

### 1. Interface translation — WordPress gettext

Use the `great-marketrealm-companion` text domain for player-facing application chrome:

- navigation and buttons;
- labels and headings;
- validation/status messages;
- accessibility labels and helper text;
- authentication/account interface copy.

PHP interface strings should use WordPress i18n helpers such as `__()`, `_x()`, `esc_html__()` and `esc_attr__()` with the literal plugin text domain.

### 2. Canonical content translation — curated catalogue (future phase)

MarketRealm authored material is not generic UI. Races, classes, subclasses, spells, items, lore, puns and proper nouns need translator review and may deliberately retain their English canonical names.

A future content catalogue should key translations by stable content identifier rather than by the English prose itself. Missing content translations must fall back to canonical English without blocking the translated interface.

## Locale independence

No code should test specifically for `nl_NL`, `de_DE`, `fr_FR`, etc. Language packs are data. The same architecture must support every WordPress locale.

## JavaScript

New JavaScript-facing copy should be structured so it can move to `wp.i18n`. Existing hard-coded JavaScript strings are to be migrated incrementally in the interface-audit phase rather than rewritten unsafely in one batch.

## Translation extraction

See `languages/README.md` for the WP-CLI extraction command and file naming conventions.

## JavaScript interface strings

Browser-generated copy must not be added as English-only literals. Current Character surfaces receive translated dictionaries with `wp_localize_script()` (`gmrcAdventuringSheetI18n`, `gmrcPrintableSheetI18n`, `gmrcGuildDiceI18n`, `gmrcRegistrationI18n`, and `gmrcDiceOfDestinyI18n`). A later migration to `wp.i18n` is compatible with this contract.
