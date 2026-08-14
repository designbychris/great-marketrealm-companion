# Character Lifecycle Initiative — Phase III.8.8
## The Paths of Calling

Phase III.8.8 introduces the reusable progression architecture for permanent
Calling specialisations.

Different Callings will use different language:

- Wizard — Arcane Tradition
- Druid — Circle
- Cleric — Domain
- Paladin — Oath
- Bard — College
- Warlock — Patron
- Barbarian — Path
- Marketrealm Callings — their own setting-specific Path terminology

Wizard is the reference implementation.

## Certified Calling Path

`CallingPath` is now part of the Character aggregate.

The existing Grand Catalogue metadata key:

`_gmrc_subclass`

is reused as the permanent Path storage field. This deliberately avoids
creating a second competing subclass field and means older build-profile data
hydrates automatically.

A certified Calling Path is immutable through normal Character mutation. Any
future respec/retraining system must be an explicit separate lifecycle.

## Wizard Arcane Tradition

The Wizard progression definition requires an Arcane Tradition at Level 2.

The choices come directly from the existing Marketrealm Grand Catalogue:

- School of Kraft Processed Magic
- School of Aromancy
- School of Emulsimancy
- School of Preservation
- School of Shelfmancy
- Boneweaver
- School of Infusion
- School of Decay & Preservation

No placeholder subclasses are invented.

## Path Folio

When a Wizard reaches the required Path level and has no certified path, the
Rising Register adds:

**Arcane Tradition Folio**

The player chooses exactly one option. The selection is stored in the durable
pending advancement and the Advancement Seal cannot become Ready until the
Path Folio is complete.

The permanent Character remains untouched until Guild Certification.

## Catch-up rule

Some Characters may already be beyond the Path selection level because they
were advanced before Phase III.8.8 existed.

If a Wizard has:

- current Level >= 2
- no certified Calling Path

the next advancement presents the Arcane Tradition Folio as a catch-up
requirement.

This lets existing characters such as earlier test Wizards enter the new
progression architecture without database surgery.

## Guild Certification

Certification:

1. rebuilds and validates all folios,
2. requires the Advancement Seal,
3. reads the pending Path choice,
4. enters the Calling Path into the Character aggregate,
5. applies spell/cantrip choices,
6. applies Vitality and Level,
7. saves the Character once,
8. archives the certified Calling Path,
9. clears the pending advancement.

The Open Ledger can then show the certified Path beside the Character Calling.

## Reuse

The framework is intentionally generic. Adding another class requires a Path
progression definition and catalogue choices rather than another advancement
controller.
