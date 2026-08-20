# Phase III.12.13A — The Artificer Specialisation Register

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.12.13A establishes the Artificer Specialisation selection boundary
without implementing Specialisation Gifts ahead of their dedicated phase.

## Canonical Register

The Great Marketrealm Player’s Handbook catalogue supplies four Artificer
Specialisations:

1. The Spice Engineer
2. The Cheesemonger
3. The Sous-Sorcerer
4. The Culinary Engineer

The Artificer chooses a Specialisation at Level 3.

The progression register uses:

- label: `Artificer Specialisation`
- folio: `Specialist Workshop Folio`
- choice key: `artificer-specialisation`
- selection level: `3`

## Living Ledger

The Arcana folio now carries a read-only Artificer Specialisation Register.

It presents:

- the chosen or upcoming Specialisation;
- the four registered candidates;
- prepared Intelligence-based spellcasting orientation;
- Infuse Item readiness;
- The Right Tool for the Job;
- Tool Expertise;
- the next major workshop milestone;
- an explicit III.12.13B boundary for Specialisation Gifts.

No Specialisation Gift mechanics are introduced here.

## Architecture

`ArtificerSpecialisationProgression` joins the shared
`PathProgressionCatalogue`.

The existing `PathCandidateCatalogue` remains the single source used to read
Artificer subclass identities from the Player’s Handbook catalogue.

`ArtificerSpecialisationRegisterPresenter` projects this certified selection
boundary into the Character Ledger without creating a parallel persistence
model.

## Next Slice

**Phase III.12.13B — Artificer Specialisation Gifts**

That phase may populate the shared `PathGiftCatalogue` with the canonical
specialist mechanics and the supplied 3 / 5 / 9 / 15 progression.
