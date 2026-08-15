# Phase III.9.3 — The Sealed Chronicle

## Purpose

The Living Register now preserves a readable chronicle of every completed Guild Certification, rather than exposing only the most recent Fresh Ink entry.

## Boundary

The Sealed Chronicle is a read-only projection of `AdvancementHistoryRepository` data. It does not persist new progression state, inspect pending advancement folios, or mutate the Character aggregate.

The progression responsibilities remain distinct:

- **Rising Register** — pending and future advancement.
- **Fresh Ink** — the newest completed certification.
- **Sealed Chronicle** — the complete retained certification history, newest first.
- **Character aggregate** — authoritative permanent character state.

## Chronicle entries

Each projected certification can describe:

- certification sequence;
- source and target levels;
- maximum HP gained;
- spells learned;
- cantrips learned;
- Path Gifts granted;
- certification timestamp;
- whether the entry is the latest certification.

The repository currently retains the latest twenty certifications, so the Chronicle naturally observes that existing storage policy without introducing a second archive.

## Presentation

The Character Ledger renders the Chronicle on the Living Register page. The latest entry receives a distinct visual treatment while older certifications remain visible beneath it in reverse chronological order.

The former raw `gmrc-rise-certification-history` presentation has been removed from the page so certification history has one presentation owner.

## Future use

The Chronicle gives later phases a stable historical surface for milestones, seals, memorable advancement events, achievements, and future Guild Diceworks integrations without coupling those systems to pending advancement paperwork.
