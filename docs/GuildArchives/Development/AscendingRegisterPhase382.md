# Character Lifecycle Initiative — Phase III.8.2
## The Rising Folios

The Advancement Ledger can now describe an advancement as a collection of
independent Guild folios rather than a single undifferentiated level-up form.

### Folio framework

- `AdvancementFolio` represents one advancement concern.
- `FolioStatus` provides ready, attention and information states.
- `FolioCollection` calculates completion and attention counts.
- `RisingFolioBuilder` assembles the folios relevant to the target level.

### First concrete folios

**Vitality Folio**

Always requires player attention. It records the class Hit Die, Constitution
modifier, current maximum HP and class-average increase. It exposes two future
choices: take the class average or roll the Hit Die.

The actual choice is not persisted in Phase III.8.2.

**Proficiency Folio**

Requires no player decision. It compares current and target proficiency and
describes whether the bonus changes at the target level.

### Safety boundary

The Rising Folios are descriptive only. They identify what is ready and what
requires attention, but the advancement commit remains locked.

Class features, spell progression, subclass/path choices and talent/ASI
decisions will join the same folio collection in later Ascending Register
passes.
