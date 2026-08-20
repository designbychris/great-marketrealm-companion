# Phase III.12.13C — Artificer Specialisation Gifts on the Living Ledger

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

This slice projects the certified III.12.13B Artificer Specialisation Gifts
into the active Character Ledger without changing advancement persistence.

## Living Projection

The Artificer Specialisation Register now includes a read-only Living
Specialisation Gifts surface.

It shows:

- every supplied Specialisation Gift available at the Artificer’s current level;
- the full player-facing mechanical detail for each available Gift;
- only the next actually supplied Specialisation Gift milestone;
- a completion state when no later supplied Specialisation Gift exists.

## Canonical Boundaries

Full Specialisations retain their supplied 3 / 3 / 5 / 9 / 15 progressions.

The Sous-Sorcerer remains deliberately uneven. Its two supplied Level 3 Gifts
are shown at Level 3 and its Specialisation progression then reports complete.
No Level 5, 9 or 15 Gifts are invented.

## Architecture

`ArtificerSpecialisationGiftLedgerPresenter` reads from the shared
`PathGiftCatalogue`. It does not modify `PathGifts`, advancement records or
the certified Calling history.

## Next Slice

**Phase III.12.13D — The Artificer’s Final Certification**

That final seal may audit the complete Artificer Calling, Specialisation
Register, Gifts, Living Ledger and browser-facing presentation end-to-end.
