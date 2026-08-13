# Character Lifecycle Initiative — Phase III.8.5
## The Guild Certification

Phase III.8.5 is the first Ascending Register phase that deliberately changes
the permanent Character record.

## Certification contract

A Guild Certification request never trusts values posted by the browser.

The service reloads and validates:

1. the saved pending advancement,
2. the Character's current certified level,
3. the immediate target level,
4. all saved Choice Folios,
5. the rebuilt Advancement Ledger,
6. the rebuilt Advancement Seal.

Certification is refused unless the Seal is still ready.

## Permanent changes

The first certification pass applies:

- exactly one Character Level,
- the certified Vitality / HP gain,
- naturally derived proficiency from the new Level.

Experience is retained.

For the class-average Vitality choice the existing D&D-style average is used.
For the Hit Die choice, the die is rolled during certification and the
Constitution modifier is applied, with a minimum gain of 1 HP.

## Replay protection

The pending advancement records `from_level` and `target_level`.

Guild Certification requires:

`pending.from_level === character.level`

and:

`pending.target_level === character.level + 1`

The Character is persisted before the pending record is cleared. Therefore,
even if cleanup were interrupted after persistence, replaying the same
certification cannot apply it twice because the Character's certified Level
has already changed.

Completed advancement history also uses a deterministic certification key to
avoid duplicate archive entries.

## Guild Archive

Completed certifications are stored under:

`_gmrc_advancement_history`

The Rising Register now displays historical Guild Certifications with their
Level transition and HP gain.

## Next architecture

The certification pipeline is deliberately generic. As later phases add
Spellcraft, Path, Talent and class-feature folios, their validated choices can
join the same final certification transaction.
