# Phase III.16.7 — The Guild Field Guide

## Certified incoming baseline

- 3,537 tests
- 12,213 assertions
- all green

## Purpose

The Guild Field Guide gives signed-in adventurers a spoiler-safe illustrated creature reference inside the Guild Library without exposing Dungeon Master combat records.

## Safety boundary

The player-facing Field Guide is a projection of the canonical Bestiary, not a second monster database. Its service whitelists only the canonical key, creature name, broad creature type/size, Steward-authored player description, and canonical artwork attachment ID. AC, HP, CR, abilities, attacks, traits, resistances, legendary actions, lair actions, source issues, and Steward/DM notes never cross the projection seam.

## Steward controls

Each canonical creature gains two editorial controls:

- **Visible in the Guild Field Guide** — opt-in publication for signed-in Guild adventurers.
- **Player-safe description** — spoiler-safe lore written specifically for players.

Restoring a Bestiary record to the Dungeon Master Guide baseline also removes its Field Guide publication override.

## Guild Library experience

The Guild Library gains a first-class Field Guide shelf with search, illustrated creature cards, accessible empty states, and individual lore folios. Creatures without artwork receive a deliberate Steward-awaiting placeholder. The folio explicitly identifies itself as the Adventurer's edition and keeps combat records sealed.

## Future seam

The publication flag is intentionally compatible with a later campaign-discovery layer. A future Market Pass/campaign feature may further restrict visible entries to creatures discovered by a campaign without changing the canonical monster record or player-safe projection contract.
