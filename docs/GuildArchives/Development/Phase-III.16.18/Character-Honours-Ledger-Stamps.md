# Phase III.16.18 — Character Honours & Ledger Stamps

Phase III.16.18 extends the certified Guild Honours foundation from account-level deeds into the individual Character Ledger.

## Purpose

The Book of Deeds remains the account-wide Guild archive. Character Honours are a separate, per-adventurer record of milestones that the Companion can prove directly from the Character's certified state.

## Foundation

- A canonical `CharacterHonourRegistry` defines the initial six distinctions.
- `CharacterBookOfDeeds` evaluates only certified Character facts: current level and whether a Calling Path has been chosen.
- `CharacterHonourLedger` stores append-only distinctions beneath the owning Guild account, keyed by the Character's stable identifier.
- Once a distinction is stamped, later state changes do not silently remove the historical certification.
- Dungeon Master read-only Character projections do not certify or mutate another Player's Character honours.

## Initial distinctions

1. **First Footfall** — reach level 1.
2. **Calling Answered** — choose a Calling Path.
3. **Seasoned Adventurer** — reach level 5.
4. **Marketrealm Veteran** — reach level 10.
5. **Hero of the Shelves** — reach level 15.
6. **Legend of the Aisles** — reach level 20.

## Ledger presentation

The sixth Character Ledger folio is now a real Character Honours page. Earned distinctions appear as wax stamps with their certification date; unwitnessed milestones remain visible as future distinctions. The folio also links back to the account-level Book of Deeds so the two archives remain clearly separate.

## Boundaries

This phase does not add manual achievement awarding, hidden achievements, DM-granted medals, or campaign-event triggers. Those can build later on the same registry/ledger foundation without creating a parallel system.
