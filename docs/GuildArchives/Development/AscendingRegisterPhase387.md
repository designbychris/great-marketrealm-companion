# Character Lifecycle Initiative — Phase III.8.7
## The Spellbook Folios — Wizard Reference Pass

Phase III.8.7 introduces the universal spellcasting advancement architecture
with Wizard as the first implemented casting model.

## Existing Arcane Pantry is authoritative

The Spellbook Folios reuse `ArcaneAbilityCatalogue`. They do not introduce a
second spell list.

The existing Wizard entries are now explicit spell-learning candidates:

- Produce Spark — cantrip
- Pantry Ward — Level 1 spell
- Market Missile — Level 1 spell

The catalogue has gained explicit `spellLevel` metadata so later spellcasting
Callings can use the same source safely.

## Wizard spellcasting definition

`WizardSpellcastingProgression` describes:

- casting model: spellbook
- two spells learned at every Wizard advancement level
- one additional cantrip at Wizard Levels 4 and 10
- maximum spell level available by Wizard level
- stable pending-choice keys

The contract is intentionally generic. Cleric, Druid, Warlock, Bard,
Sorcerer and other casting models can be added without changing the
Advancement Ledger controller.

## Real Choice Folios

Wizard advancement can now add:

- `Spellbook Folio` — choose-N new spells
- `Cantrip Folio` — when that Wizard level grants another cantrip

These folios use the same durable pending advancement as Vitality. The
Advancement Seal cannot become Ready while a required Spellbook Folio is
incomplete.

## Character spellbook

`Spellbook` is now part of the Character aggregate and is persisted by the
normal `CharacterRepository` under `_gmrc_spellbook`.

Guild Certification applies pending Wizard spell/cantrip choices to the
Character before the existing single Character save. This keeps Level, HP and
spell learning inside the same aggregate persistence boundary.

The Arcane Pantry continues to show its catalogue, but entries permanently
learned through Guild Certification are marked **In Spellbook**.

## Safe initial catalogue behaviour

The first Wizard pass only offers spells that actually exist in the current
Marketrealm Arcane Pantry. It does not silently import or invent a complete
external Wizard spell list.

This is deliberate. The advancement machinery is now real; expansion of the
Marketrealm spell catalogue can happen independently without changing the
Spellbook Folio engine.
