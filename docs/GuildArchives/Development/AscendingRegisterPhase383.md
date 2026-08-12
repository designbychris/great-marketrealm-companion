# Character Lifecycle Initiative — Phase III.8.3
## The Choice Folios

The Rising Folios can now record temporary player decisions without mutating
the Character.

### Generic choice contract

`ChoiceMode` supports:

- single choice
- multiple choice
- choose-N

`ChoiceRequirement` owns option validation, normalisation and cardinality.

This contract is intentionally generic so future Spellcraft, Path, Talent and
class-feature folios can reuse the same behaviour.

### Temporary advancement storage

`AdvancementChoiceStore` keeps choices in the current GMRC session, scoped by:

- Character ID
- target level
- choice key

Selections therefore belong to the pending advancement rather than the saved
Character.

### First interactive folio

The Vitality Folio is now interactive.

The player may record:

- take the class average
- roll the class Hit Die

Once one valid method is recorded, Vitality changes from **Decision required**
to **Ready**, and the Rising Folios completion count updates.

The choice can be changed before advancement certification.

### Safety boundary

Phase III.8.3 still never changes Level, HP, proficiency, spells or class
features. Even when every current folio is ready, `commit_available` remains
false. A later Ascending Register phase will review and atomically certify the
complete advancement.
