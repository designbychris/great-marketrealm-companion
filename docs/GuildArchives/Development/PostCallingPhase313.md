# Phase III.13 — The Post-Calling Expansion

## Guild Seal

**Status:** Foundation implemented — awaiting server PHPUnit certification.

Phase III.13 begins immediately after the successful completion of the
specialist Calling programme.

Certified Calling baseline entering this phase:

- 13 specialist Callings
- 3,240 tests
- 10,313 assertions
- all green

## Purpose

The Post-Calling Expansion gives world-reference content a first-class home
outside character-specific workflows.

The initial foundation deliberately does **not** rename spells, import new
backgrounds or expand equipment. Those content migrations belong to their
dedicated slices.

## The Guild Library

A new `library` Kingdom provides a real application route and navigation
entry named **Guild Library**.

The foundation registers three reference domains:

1. **Sage's Spellbook** — Phase III.13.1
2. **Background Register** — Phase III.13.3
3. **The Marketrealm Armoury** — Phase III.13.4

Every domain identifies:

`The Great Marketrealm - Players Handbook`

as its canonical source.

Each foundation catalogue contains zero imported entries. This protects the
3,240-test Calling baseline from accidental content migration during the
architecture push.

## Shared Contract

All future reference catalogues implement
`ReferenceCatalogueInterface`.

`ReferenceLibraryRegistry` owns the application-level catalogue collection,
while character creation, advancement and Living Ledger systems remain
independent consumers.

This prevents Sage's Spellbook or the Armoury from becoming giant
character-form-only data structures.

## Planned Post-Calling Sequence

- III.13 — Post-Calling Foundation
- III.13.1 — Sage's Spellbook
- III.13.2 — Spell Integration
- III.13.3 — Expanded Backgrounds
- III.13.4 — The Marketrealm Armoury
- III.13.5 — Relics of the Marketrealm
- III.13.6 — Post-Calling Certification

## Preservation Boundary

This phase must not:

- rename or replace existing spell records;
- change character spell selections;
- add background choices;
- add, remove or rebalance carried equipment;
- alter Calling progression;
- alter advancement history;
- alter Living Ledger mechanics.

Those changes require their own certified phases.
