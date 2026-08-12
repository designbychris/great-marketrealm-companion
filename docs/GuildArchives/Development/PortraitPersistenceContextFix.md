# Portrait Studio — Persistence Context Fix

## Symptom

Character Creation correctly updated the provisional Guild Record with the
selected Race and Class, but the Guild Illuminator remained at "Awaiting
Subject". Browser inspection showed:

- `data-portrait-persisted="true"`
- empty `data-portrait-race`
- empty `data-portrait-class`

## Cause

The persisted-portrait lifecycle guard originally inferred persistence from
the presence of `PortraitViewModel`.

Character Creation also renders a provisional `PortraitViewModel`, so the
creator was incorrectly classified as a saved portrait. The live Character
Creator engines then skipped initialisation by design.

## Contract

Persistence is now explicit at the page boundary:

- Create Adventurer: `portraitPersisted => false`
- Edit Adventurer: `portraitPersisted => true`
- Open Ledger: `portraitPersisted => true`
- Delete confirmation: `portraitPersisted => true`

The shared portrait component no longer guesses persistence from the model
type.

This preserves the Private Studio fix while restoring live Character Creation.
