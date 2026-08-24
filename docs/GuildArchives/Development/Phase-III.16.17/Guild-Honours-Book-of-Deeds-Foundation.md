# Phase III.16.17 — Guild Honours & Book of Deeds Foundation

Phase III.16.17 opens the final planned room in the Guild Hall: **Guild Honours**.

## Certified scope

- Adds a dedicated `Honours` Kingdom and `/guild-honours` route.
- Introduces a canonical `GuildHonourRegistry` rather than embedding honour definitions in presentation code.
- Projects eligibility from the already-certified `GuildMembershipSummary` so Character, Campaign and Fellowship relationships are not reimplemented.
- Persists newly certified deeds in WordPress user metadata through `GuildHonourLedger`.
- Treats the Book of Deeds as append-only: once a deed is certified it remains part of the account's historical Guild record.
- Adds the first six canonical honours, with the Campaign Steward distinction restricted to Dungeon Master accounts.
- Converts the Guild Hall's former planned Honours card into a live Book of Deeds destination.
- Retires stale Character Ledger copy that still described inventory, progression and Guild Honours as future work.
- Keeps this phase account-level. Character-specific wax-stamp placement may build on the same register later.

## Initial honours

1. **First Name in the Ledger** — at least one Character inscribed.
2. **A Shelf of Stories** — at least three Characters recorded.
3. **At the Campaign Table** — participation in or stewardship of at least one Campaign.
4. **Fellowship Forged** — membership in or stewardship of at least one Fellowship.
5. **A Tale Entered in the Archives** — at least one archived Campaign relationship.
6. **Keeper of the Campaign Ledger** — Dungeon Master stewardship of at least one Campaign.

## Security and ownership

The Book of Deeds is a read-only account surface. It does not expose mutation routes, cross-account Character access, Campaign management, or Fellowship management. New honours are certified only from relationships the current account can already prove through existing Companion services.

## Future extension

Later phases may add Character-specific stamps, custom DM-awarded honours, hidden honours, campaign-specific distinctions, or event-driven certification. Those extensions should reuse the canonical registry and persistent ledger rather than creating a second achievement system.
