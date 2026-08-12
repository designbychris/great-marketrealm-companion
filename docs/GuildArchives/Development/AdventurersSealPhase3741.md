# Character Lifecycle Initiative — Phase III.7.4.1
## The Adventurer's Seal

When all seven Guild folios are ready, the Complete Adventurer audit now
derives a certified state and displays the Adventurer's Seal.

Certification is never stored independently; it is a direct consequence of
the 7/7 Registrar audit, so the seal cannot drift out of sync with the record.

The seal reuses Auby's existing gold Seal of Approval and therefore inherits
the existing visible-trigger animation and reduced-motion behaviour.

## Marketrealm Language Registry

The authoritative selectable language catalogue now contains:

- Common
- Fructan
- Vegcant
- Mycelian
- Dairy Tongue
- Meat Speech
- Shelf Script

Removed:

- Dwarvish
- Elvish
- Giant
- Gnomish
- Goblin
- Halfling
- Orc

Character Creation and Edit Adventurer both consume `Language::all()`, keeping
the UI and validation rules on the same source of truth.
