# Phase III.12.1 — The Class Framework Audit

Phase III.12 — **The Paths of the Adventurer** begins by making GMRC's current
class implementation state explicit.

## Registered Callings

`CharacterClass` currently recognises 15 playable Callings:

1. Grocer
2. Cleaver Saint
3. Artificer
4. Barbarian
5. Bard
6. Cleric
7. Druid
8. Fighter
9. Monk
10. Paladin
11. Ranger
12. Rogue
13. Sorcerer
14. Warlock
15. Wizard

All fifteen already participate in the shared Character identity, hit-die,
saving-throw and generic advancement foundation.

## Current specialist implementation

The audit does not infer tabletop rules.

It asks GMRC's existing progression catalogues what is actually implemented.

At the start of III.12:

### Wizard

Wizard currently has:

- specialist Calling advancement metadata;
- specialist spellcasting progression;
- registered Calling Path progression;
- existing spellbook/cantrip/path folio integration built by earlier phases.

Wizard therefore reports as a **specialist** implementation.

### Remaining Callings

The other fourteen Callings currently resolve through
`RegisteredCallingProgression`.

That definition intentionally provides:

- class identity;
- level identity;
- a `registered` catalogue status;
- no invented automatic gains;
- no invented delegated choices.

They therefore report as **foundation** implementations until a specialist
definition is deliberately added.

This includes the Marketrealm-native Grocer and Cleaver Saint. Their class
definitions already describe identity and starting traits elsewhere in GMRC,
but III.12 does not pretend that a full level-by-level progression has already
been encoded.

## New Class Capability Catalogue

Phase III.12.1 introduces:

- `ClassCapabilityProfile`
- `ClassCapabilityCatalogue`
- `ClassFrameworkAudit`

The capability catalogue derives its answers from the existing:

- `ClassProgressionCatalogue`
- `SpellcastingProgressionCatalogue`
- `PathProgressionCatalogue`

It does not hard-code a Wizard special case.

As future specialist class definitions are registered, the audit will change
with the application.

## Capability vocabulary

Each Calling can currently report:

### Advancement

The existing class catalogue status, currently:

- `reference` for Wizard;
- `registered` for foundation Callings.

### Spellcasting progression

Whether GMRC has a specialist spellcasting progression definition for that
Calling.

### Calling Path progression

Whether GMRC has a registered Path progression definition for that Calling.

### Implementation state

- `specialist` — one or more specialist progression surfaces exist;
- `foundation` — the Calling is registered but still relies entirely on the
  shared generic progression foundation.

## Why the audit comes first

The purpose of III.12 is not to clone the Wizard implementation fourteen
times.

Different Callings need different combinations of:

- automatic class features;
- class resources;
- path/subclass decisions;
- spellcasting;
- known/prepared spell handling;
- advancement choices;
- Ledger presentation.

The audit gives later phases a stable capability boundary so shared
infrastructure can be reused and class-specific rules can remain isolated.

## Recommended build order after III.12.1

The next slice should introduce the first reusable specialist progression
contract beyond Wizard.

A strong first candidate is **Fighter**, because it exercises:

- automatic class features;
- martial progression;
- a Calling Path/subclass;
- advancement choices;
- no baseline spellbook dependency.

That makes Fighter a useful test that the framework is genuinely generic
rather than merely Wizard-shaped.

After Fighter, the framework can expand into increasingly different capability
families such as Cleric/Druid prepared spellcasting, Rogue expertise-style
choices, Barbarian/Monk resources, and the Marketrealm-native Grocer and
Cleaver Saint progressions.
