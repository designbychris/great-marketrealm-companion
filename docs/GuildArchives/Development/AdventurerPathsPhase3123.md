# Phase III.12.3 — The Barbarian's Calling

Phase III.12 promotes Barbarian from the generic Calling foundation to its own
specialist progression.

## Fresh-repository basis

The fresh green repository already provides the following Barbarian-specific
material:

- Barbarian is a registered `CharacterClass` with a d12 hit die;
- Strength and Constitution are its registered saving-throw proficiencies;
- the shared ability catalogue already contains a `Rage` feature for
  Barbarian;
- the player catalogue already registers eight Barbarian Paths.

Those existing records are preserved rather than duplicated.

## Existing Barbarian Paths

The fresh player catalogue contains:

1. Path of the Great Tony
2. Path of the Expired
3. Path of the Marbled Rage
4. Path of the Rind
5. Path of the Butchered Rage
6. Path of the Sugarrush
7. Path of the Pickled Rage
8. Path of the Butterbound

The source catalogue currently provides Path identity and descriptions, but no
Path trait rules. III.12.3 therefore does not invent Path Gifts for them.
Those remain reserved for III.12.3B.

## Specialist progression policy

III.12.3 adds `BarbarianProgression` to GMRC's progression catalogue.

The specialist progression metadata follows the familiar Barbarian
level-progression model used by the Companion implementation. This progression
metadata is newly encoded in III.12.3; it was not present in the supplied
player catalogue.

The major automatic entries include:

- Level 2 — Reckless Attack and Danger Sense
- Level 5 — Extra Attack and Fast Movement
- Level 7 — Feral Instinct
- Levels 9 / 13 / 17 — Brutal Critical progression
- Level 11 — Relentless Rage
- Level 15 — Persistent Rage
- Level 18 — Indomitable Might
- Level 20 — Primal Champion

These remain Calling reference metadata. They do not introduce a duplicate
permanent feature store.

## Primal Path

Barbarian now registers a shared Path progression:

- label: **Primal Path**
- folio: **Primal Path Folio**
- choice key: `barbarian-primal-path`
- selection level: 3

The shared `PathCandidateCatalogue` supplies the eight existing Barbarian
Paths.

## Shared Measure of Growth

Barbarian delegates growth decisions to the existing Measure of Growth at:

- Level 4
- Level 8
- Level 12
- Level 16
- Level 19

No Barbarian-specific ability-score/talent system is created.

## Reserved Path Gift milestones

The existing Barbarian source catalogue does not yet define actual Path Gifts.

III.12.3 records future Path Gift responsibility at:

- Level 6
- Level 10
- Level 14

Those hand-offs are marked for III.12.3B.

The Level 3 Path's first gift is deliberately not fabricated in this slice;
III.12.3B can add it once each of the eight Paths receives an explicit gift
definition.

## Rage boundary

Rage already exists in the shared ability catalogue.

III.12.3 does **not** yet persist current Rage uses or whether the Barbarian is
actively raging.

That belongs to the planned active-play slices:

- III.12.3A — Rage Register presentation
- III.12.3C — persistent Fury/Rage reserves and active Rage state
- III.12.3D — interactive Barbarian actions

This keeps permanent advancement separate from current combat state.

## Class Framework Audit

The audit now derives:

- specialist Callings: 3
- foundation Callings: 12
- Wizard: specialist
- Fighter: specialist
- Barbarian: specialist

Barbarian remains a non-spellcasting specialist with a Calling Path.

The audit implementation itself remains unchanged.
