# Character Lifecycle Initiative — Phase III.8.4
## The Advancement Seal

Phase III.8.4 separates **durable advancement paperwork** from the permanent
Character and adds the Registrar's final review state.

## Pending Advancement

A pending advancement is now stored with the WordPress Character post under:

`_gmrc_pending_advancement`

The record contains:

- schema version
- Character ID
- current/from level
- target level
- recorded Choice Folio selections

This means leaving the Advancement Ledger, visiting another Companion page,
or returning to the Rising Register does not discard the level-up paperwork.

If an eligible character's current/target level no longer matches an older
pending record, the repository starts a fresh pending advancement rather than
applying stale choices.

## Phase III.8.3 migration

The previous Choice Folio implementation used PHP session storage. When the
Advancement Ledger is opened, III.8.4 checks for a legacy session choice if no
durable choices exist and migrates it into the pending advancement.

## The Advancement Seal

`AdvancementSealPresenter` performs the Registrar's review.

When every current Rising Folio is ready, the page displays the gold Auby
Advancement Seal and the state:

**READY FOR GUILD CERTIFICATION**

The review currently summarises:

- current level → target level
- Vitality / HP method
- proficiency change
- folio completion count

The seal is deliberately not the final Character mutation.

## Safety boundary

Phase III.8.4 still does not change:

- Character Level
- current or maximum HP
- proficiency-derived calculations
- class features
- spells
- subclass/path
- any other permanent Character field

`commit_available` remains false.

Phase III.8.5 will own the atomic Guild certification that applies a reviewed
pending advancement to the Character and then clears the pending paperwork.
