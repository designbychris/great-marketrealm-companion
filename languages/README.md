# Great Marketrealm Companion language packs

English is the canonical source language for the Companion interface.

WordPress gettext catalogues for this plugin use the text domain:

`great-marketrealm-companion`

## File names

Compiled locale packs should follow WordPress conventions, for example:

- `great-marketrealm-companion-nl_NL.po` / `.mo`
- `great-marketrealm-companion-de_DE.po` / `.mo`
- `great-marketrealm-companion-fr_FR.po` / `.mo`
- `great-marketrealm-companion-es_ES.po` / `.mo`

JavaScript catalogues may be added later using WordPress JSON translation files when a script has been migrated to `wp.i18n`.

## Interface vs MarketRealm content

This directory is for **application interface** language: buttons, labels, navigation, status messages, validation copy and accessibility text.

Race/class names, spells, subclasses, setting lore, item descriptions and other authored MarketRealm material are **canonical content**, not interface chrome. They will use a separate curated content-translation pipeline so puns, proper nouns and rules text can be reviewed deliberately per language.

## Generating a POT catalogue

From the plugin root, with WP-CLI's i18n command available:

```bash
wp i18n make-pot . languages/great-marketrealm-companion.pot --domain=great-marketrealm-companion --exclude=vendor,node_modules
```

Do not hand-edit generated POT files. Translators work from PO files and compiled MO files.
