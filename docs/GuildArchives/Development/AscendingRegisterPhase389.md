# Character Lifecycle Initiative — Phase III.8.9
## The Gifts of the Path

Phase III.8.9 teaches the Ascending Register what a certified Path of Calling
actually grants.

The Path itself remains owned by Phase III.8.8. This phase owns the permanent
features unlocked by that Path.

## Reference Path — School of Shelfmancy

The first Gift catalogue follows the existing Marketrealm Player’s Handbook
milestones:

- Level 2 — Spell-Stored Container
- Level 2 — Packaging Proficiency
- Level 6 — Vacuum Lock
- Level 10 — Dimensional Pantry
- Level 14 — Master of the Endless Aisles

The feature text is represented as Path-gift records rather than being
hard-wired into the Wizard controller.

## Permanent PathGifts

`PathGifts` is now part of the Character aggregate and is persisted under:

`_gmrc_path_gifts`

The stored value contains stable gift keys. Display copy is resolved from the
Gift catalogue so rules text can evolve without rewriting every Character.

Guild Certification is the only normal flow that adds Path gifts.

## Gifts of the Path Folio

The Rising Register adds a `path-gifts` folio only when the selected/certified
Path has implemented gifts to grant.

For Shelfmancy, the folio:

- is automatic and therefore Ready,
- lists each newly unlocked feature,
- explains the feature in expandable Guild notes,
- participates in the Advancement Seal,
- becomes permanent only during Guild Certification.

The Path Folio still owns the actual Arcane Tradition choice. The Gifts Folio
never duplicates that decision.

## Catch-up behaviour

Existing characters can already be above the level where a feature should have
been granted.

If a certified Shelfmancer has no stored Level-2 gifts, the next advancement
adds them as a Registrar catch-up.

For the current test Wizard, already Level 3 with School of Shelfmancy, the
next advancement to Level 4 will therefore surface:

- Spell-Stored Container
- Packaging Proficiency

Once certified, they will not appear again.

## Future Path support

The Gift catalogue is path-based, not Wizard-specific. Additional Path
definitions can use the same contract for:

- Cleric Domains
- Druid Circles
- Paladin Oaths
- Bard Colleges
- Warlock Patrons
- Barbarian Paths
- Marketrealm-specific Calling specialisations

Definitions currently mark gifts as `automatic`. A later Gift definition that
requires a player decision can delegate that decision into the existing Choice
Folio machinery rather than adding another advancement system.

## Roadmap note

The Measure of Growth was previously labelled Phase III.8.9 in the early
Wizard progression placeholders. With Gifts of the Path now occupying III.8.9,
Measure of Growth moves to Phase III.8.10.
