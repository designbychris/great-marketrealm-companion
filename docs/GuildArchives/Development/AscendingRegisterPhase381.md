# Character Lifecycle Initiative — Phase III.8.1
## The Advancement Ledger

Phase III.8 begins by separating earned experience from Guild-certified level.

Before this phase, `Character::gainExperience()` immediately increased level,
maximum hit points and proficiency-derived systems whenever an XP threshold
was crossed. That prevented the player from making level-up decisions.

The new contract is:

1. Experience is recorded.
2. Crossing a threshold makes the Character eligible for advancement.
3. The existing certified level remains unchanged.
4. The Rising Register exposes **Begin Advancement**.
5. The Advancement Ledger previews the next single level.
6. No mutation is committed in Phase III.8.1.

Large XP awards may unlock several levels, but certifications are queued and
processed one level at a time.

`ClassProgressionCatalogue` establishes a source-of-truth boundary for future
class-specific automatic gains and choices. The foundation deliberately leaves
those arrays empty rather than guessing rules not yet imported into the
Marketrealm progression catalogue.
