# Phase III.13.1A — The Spell Register

## Guild Seal

**Status:** implementation patch prepared — awaiting server PHPUnit certification.

This slice turns the empty `SpellReferenceCatalogue` seam into Sage's canonical,
read-only Spell Register. It deliberately does not migrate Character Creation,
advancement, Living Ledger spellbooks or active-play spell selection; that remains
Phase III.13.2.

## Canonical source

`The Great Marketrealm - Players Handbook` is transcribed without silently filling
missing spell metadata from external 5e knowledge.

The handbook supplies **73 source records representing 71 canonical spell
identities**:

- 29 renamed/reskinned spell identities;
- 42 original Marketrealm spell identities;
- two duplicated identities with conflicting source variants.

The original Marketrealm catalogue currently runs from cantrips through 6th-level
spells. No 7th-, 8th- or 9th-level Marketrealm entries are invented by this phase.

## Source anomalies retained

`Bread Wall` appears twice at 2nd level with materially different cover, dimensions
and durability mechanics. Both variants are retained beneath the `bread-wall`
identity.

`Vacuum Seal` appears once as a 3rd-level restraining pressure field and once as a
4th-level long-duration preservation seal. Both variants are retained beneath the
`vacuum-seal` identity.

`Oven of Annihilation` lists `Arificer` in the supplied source. The Register keeps
that label visible and marks it as a source issue rather than silently changing it.

Where the handbook does not state level, school or access labels, the canonical
record contains `null`/an empty list and an explicit source-issue marker.

## Architecture

`SpellRecord` owns one stable spell identity. `HandbookSpellRegister` owns lookup,
kind/level filtering and source-variant counts. The catalogue projects those records
through the already-certified `ReferenceCatalogueInterface` seam.

The Guild Library can therefore report real Spell Register counts now while the
character-specific arcana systems remain untouched.
