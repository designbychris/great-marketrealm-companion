# Character Lifecycle Initiative — Phase III.7.4
## The Complete Adventurer

The final character-lifecycle phase begins with the Registrar's Final Audit.

Rather than introducing another character data model, the audit proves that
all existing systems can describe the same persisted adventurer together:
Identity, Ability Scores, Guild Illuminator portrait, Equipment, Combat,
Spells & Abilities, and Progression.

The Open Ledger now receives a `CompleteAdventurerPresenter` summary and shows
seven compact folio cards. Each card can jump directly to the relevant Ledger
tab. Empty inventory, no equipped weapon, or a non-spellcasting class are
valid states rather than false errors.

This is intentionally a consistency layer over the established systems. It
does not duplicate their persistence or rules.
