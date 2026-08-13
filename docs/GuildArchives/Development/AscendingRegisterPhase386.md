# Character Lifecycle Initiative — Phase III.8.6
## The Calling Folios

Phase III.8.6 teaches the Ascending Register to ask a Calling what an
adventurer gains at a particular level.

## Progression definition contract

`ClassProgressionDefinitionInterface` separates class rules from controllers,
views and certification code.

A definition answers:

- which Calling it supports,
- the target Level,
- automatic Calling gains,
- specialist rules delegated to another folio,
- catalogue status.

`ClassProgressionCatalogue` resolves the first matching definition.

All registered Character classes remain supported. Callings whose detailed
rules have not yet been imported use `RegisteredCallingProgression`, which
returns an intentionally empty `registered` entry rather than inventing rules.

## Wizard reference Calling

`WizardProgression` is the first reference definition.

It identifies level-specific Wizard milestones while deliberately delegating
specialist decisions:

- Spellbook / Arcane Studies → Phase III.8.7
- Arcane Tradition / path features → Phase III.8.8
- Ability improvement / talents → Phase III.8.9

Phase III.8.6 does not collect those choices early.

The definition is addressable by level from 2 through 20, independently of a
live Character's current level.

## Calling Folio

Every eligible advancement now receives a third Rising Folio:

1. Vitality
2. Proficiency
3. Calling

The Calling Folio is informational and Ready. It reports:

- Calling label,
- target Level,
- catalogue status,
- automatic-gain count,
- delegated specialist-folio count.

Delegated future requirements are displayed separately from interactive Choice
Folios, so they cannot accidentally post through the Vitality choice route.

## Advancement Seal and certification

The Advancement Seal now reviews the Calling alongside Level, Vitality and
Proficiency.

Guild Certification archives the exact resolved `class_progression` snapshot
with the completed certification. This gives the Guild Archive provenance for
what the Calling catalogue knew at the time of advancement.

No new permanent class-feature mutation is introduced in III.8.6. Specialist
feature/spell/path persistence belongs to the phases that own those rules.

## Safety

Resolving a Calling Folio cannot mutate:

- Level,
- HP,
- XP,
- Character class,
- any permanent Character state.

Guild Certification remains the only advancement mutation boundary.
