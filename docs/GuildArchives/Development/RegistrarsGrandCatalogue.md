# Character Lifecycle Initiative — Phase III.7.1: The Registrar's Grand Catalogue

The Players Handbook remains the editorial source. Runtime character creation does not call Google Drive.
A curated snapshot is bundled at `resources/catalogue/players-handbook.v1.json` and imported into the WordPress database through the `gmrc_character_catalogue` option.

Snapshot totals: **14 race families**, **44 heritages/subtypes**, **15 classes**, **69 subclass/archetype paths**.

Existing canonical character race/class identifiers are preserved. Heritage and subclass are stored as additional WordPress character metadata (`_gmrc_heritage`, `_gmrc_subclass`) so the stable Character constructor does not need to change.

The Create Character form now filters heritage by selected race and future subclass path by selected class. Selecting a heritage also synchronises the existing `portrait_heritage` field, providing the input needed by Phase III.7.2 — The Illuminator's Living Palette.
