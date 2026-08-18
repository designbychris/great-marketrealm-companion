# Phase III.12.2E — The Fighter's Final Seal

III.12.2E is the hardening and integration seal for the first non-Wizard
specialist Calling.

It intentionally adds no new Fighter feature.

The purpose is to prove that the full Fighter implementation remains coherent
across progression, subclassing, active play, persistence and presentation.

## Certified Fighter architecture

The sealed Fighter implementation now spans:

### Specialist Calling progression

`FighterProgression`

This remains responsible for permanent level-based advancement reference
metadata and delegation.

Major Calling milestones include:

- Level 2 — Action Surge
- Level 3 — Martial Path
- Level 5 — Extra Attack
- Level 9 — Indomitable
- Level 11 — Extra Attack
- Level 13 — Indomitable
- Level 17 — deeper Action Surge and Indomitable reserves
- Level 20 — fourth Attack-action attack

Measure of Growth remains delegated to the shared growth system.

### Martial Path

The shared Path machinery remains responsible for the Level 3 Fighter Path
selection.

Fighter remains a specialist Calling without becoming a baseline spellcaster.

### Martial Path Gifts

All six registered Martial Paths retain the gift cadence:

- 3
- 7
- 10
- 15
- 18

The six paths remain:

- Discontinued Lineage
- Butcher
- The Carver
- Cutlery Knight
- The Vineblade
- Shelf Sentinel

Only persisted/certified gifts appear in the Martial Register.

### Martial Register

The Register remains a read model derived from:

- certified Character level;
- certified Calling Path;
- certified Path Gifts;
- persisted active resource expenditure.

It does not become another Character authority.

### Battle Reserves

`ActiveClassResourceState` stores only expenditure.

`FighterBattleReserveService` remains the single Fighter authority for maximum
resource uses and rest refresh policy.

This matters because the active state cannot become stale when the Fighter
levels.

For example, if a Level 16 Fighter has spent their one Action Surge and then
reaches Level 17, the same expenditure state naturally renders:

`1 / 2 remaining`

No resource migration is required.

### Martial Actions

`FighterMartialActionPresenter` remains separate from reserve persistence.

The sealed contracts are:

- Second Wind — Guild Diceworks `1d10 + Fighter level`
- Action Surge — no invented dice roll
- Indomitable — six saving-throw rerolls using the Character's real modifiers

## Rest boundary

Short rest restores:

- Second Wind
- Action Surge

It does not restore Indomitable.

Long rest restores every Fighter Battle Reserve.

## Security boundary

Resource spend and refresh remain POST-only Companion commands protected by
the Character resource nonce contract.

Active resource persistence remains owner-scoped.

## Class isolation

The Final Seal explicitly protects against Fighter systems leaking to Wizard.

A Wizard:

- does not receive a Martial Register;
- does not receive Fighter Martial Actions;
- cannot be resolved by Fighter Battle Reserve policy.

This establishes an important standard for the rest of Phase III.12.

## Code hygiene

III.12.2C made `FighterBattleReserveService` the authority for Indomitable
maximums.

The old private Indomitable maximum helper in
`FighterMartialRegisterPresenter` therefore became dead code and has been
removed.

A redundant same-namespace import was also removed.

No behavior changes result from this cleanup.

## Final Seal regression

The final integration regression protects:

- specialist capability composition;
- milestone boundaries;
- growth/path-gift delegation;
- Level 3 Path + gift hand-off;
- all six Martial Path gift cadences;
- Extra Attack boundaries;
- resource maximum boundaries;
- resource state surviving level changes;
- short/long rest distinctions;
- Second Wind level scaling;
- Action Surge remaining dice-free;
- Indomitable six-save rerolls;
- certified-only Path Gift rendering;
- non-Fighter isolation;
- route and nonce protection;
- responsive and forced-colors presentation;
- single resource-maximum authority.

## Reference implementation status

Once III.12.2E passes PHPUnit and the already-completed live Fighter checks,
Fighter becomes GMRC's **martial reference implementation**.

It provides reusable patterns for later classes without requiring them to copy
Fighter rules:

- specialist class progression;
- non-spellcasting Path progression;
- automatic Path Gifts;
- active class resources;
- short/long rest refresh policy;
- action contracts;
- Guild Diceworks integration;
- Character Ledger class-specific presentation.

The next Calling can reuse the architecture while supplying its own rules.
