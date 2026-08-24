# Phase III.16.15 — Companion Wayfinding & Application Completeness Certification

Phase III.16.15 certifies the Guild Hall as an accurate, role-aware directory of the application that now exists.

## Certified wayfinding

- Players receive direct Guild Hall routes to the Adventurer Register, Active Campaigns, Market Pass redemption, Fellowships, Fellowship Seal redemption, Guild Library and Guild Profile.
- Dungeon Masters receive the shared application destinations plus the Dungeon Master's Desk, without Player-only Campaign invitation controls.
- The Adventurer Register now explicitly owns the already-delivered Guild Journal and Leather Satchel experiences inside each Character Ledger instead of presenting them as unfinished Dashboard initiatives.
- Guild Honours remains the sole explicitly planned Guild Hall room and has no false destination.
- Dashboard role decisions are resolved before rendering through `GuildHallDirectory`; the view only renders certified room data.
- Keyboard focus, reduced-motion and forced-colour fallbacks protect the directory presentation.

## Boundary

This phase adds no Character mechanics, Campaign rules, Fellowship permissions or account capabilities. It certifies discoverability of existing functionality and removes stale wayfinding language.

## Incoming certified baseline

- 3,659 tests
- 13,267 assertions
- all green
- Phase III.16.14 front-end certification passed
