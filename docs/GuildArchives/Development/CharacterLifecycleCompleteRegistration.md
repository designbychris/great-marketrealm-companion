# Character Lifecycle Initiative — Phase III: The Complete Registration

Phase III closes the gap between Character Creation and The Open Ledger.

## Registration path

1. First Inscription — name.
2. Marketrealm Heritage — race.
3. Adventuring Calling — class.
4. Personal History — background.
5. Adventuring Measures — Standard Guild Array.
6. Registrar's Choices — languages and generic tool categories.
7. Guild Illuminator — portrait.
8. Final Registrar Review.
9. Seal the Guild Record.
10. Open Ledger.

## Standard Guild Array

Interactive Companion registration uses 15, 14, 13, 12, 10 and 8. Each value
must be assigned exactly once.

Legacy programmatic callers that do not send `registration_confirmed=1`
retain the established average-score defaults.

## Background choices

Background definitions already know how many languages they grant and whether
they grant generic Artisan's Tools or Gaming Set categories. Phase III finally
resolves those choices before the Guild record is sealed.

Concrete choices are persisted separately and restored into the Character.

## Editing

The same resolver appears when a player changes background through Edit
Adventurer, preventing edited records from gaining unresolved categories.
