# Phase III.9.4 — Guild Milestones

## Purpose

The Living Register now recognises meaningful moments inside the Sealed Chronicle without creating a separate achievement or progression system.

Guild Milestones are read-only marks derived from completed certification history. They never inspect pending advancement folios and never mutate the Character aggregate.

## Recognised milestones

- **First Guild Seal** — the earliest retained completed certification.
- **Calling Path Entered** — the first certification where a Calling Path appears in the sealed archive.
- **Gift of the Path** — a certification that grants one or more Path Gifts.
- **Level 5 / 10 / 15 / 20 Guild Milestones** — major certified level thresholds.

A single certification may carry more than one milestone mark when several notable events occur together.

## Presentation

Milestones appear directly on their Sealed Chronicle entry as compact margin marks. The Chronicle header also reports the total number of milestone marks currently visible in retained certification history.

This keeps the Living Register historical rather than turning it into an achievements screen. Future honours and achievements can remain a separate concern.

## Architectural boundary

The milestone layer is derived entirely by `LivingRegisterPresenter` from the immutable `AdvancementHistoryRepository` entries already used by Fresh Ink and the Sealed Chronicle.

No new post meta, repository, write path, or advancement mutation is introduced by Phase III.9.4.
