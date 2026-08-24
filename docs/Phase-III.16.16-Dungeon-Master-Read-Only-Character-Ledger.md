# Phase III.16.16 — Dungeon Master Read-Only Character Ledger

Phase III.16.16 gives a Dungeon Master a certified, non-editable Character Ledger projection for adventurers currently attached to one of their active Campaigns.

## Certified boundary

- Access requires Dungeon Master Campaign authority.
- The Campaign must belong to the current Dungeon Master.
- The Campaign must be active.
- The Character must currently appear in that Campaign's roster.
- The Character is re-resolved against the rostered Player's ownership before presentation.
- Detaching/reassigning/removing the Character removes access automatically.
- Guessing a Character identifier is never sufficient.

## Read-only projection

The DM projection reuses the canonical Character Ledger data assembly and persisted portrait pipeline, but renders through a dedicated view that contains no mutation forms or Character-owner commands.

It presents encounter-relevant identity, level, HP, AC, initiative, speed, passive perception, abilities, saving throws, skills, attacks, spellcasting, equipment, languages and tool proficiencies.

## Privacy

Player Journal/private notes are excluded from the DM projection in this phase. Character ownership and all editing authority remain with the Player.
