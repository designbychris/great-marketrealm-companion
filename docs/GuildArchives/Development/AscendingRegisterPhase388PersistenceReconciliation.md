# Phase III.8.8 — Character Persistence Reconciliation

The live Wizard test reached successful Guild Certification but the Register
and Open Ledger subsequently rehydrated the Character at the old level.

This pass treats the permanent WordPress Character record as the final
certification boundary.

## Save verification

After `CharacterRepository::save()` writes metadata, the repository now:

1. clears the WordPress post cache,
2. clears the `post_meta` object-cache entry,
3. reads `_gmrc_character_id` back from the saved post,
4. reads `_gmrc_level` back from the saved post,
5. verifies both values against the Character aggregate.

If Level 3 cannot be read back after a Level 2 → 3 certification, persistence
throws and Guild Certification cannot report success or clear the pending
advancement.

## Duplicate-domain guard

A Character ULID is the permanent Guild identity and must resolve to exactly
one published Character post for the current user.

Repository lookup now checks for up to two matches. If more than one post has
the same `_gmrc_character_id`, the operation stops with an explicit duplicate
record error rather than selecting an arbitrary WordPress post.

This protects against the failure mode where one duplicate receives Level 3
while a later Register request hydrates a different duplicate still at Level 2.

## Regression

The repository test suite now certifies a persisted Level 2 Wizard, saves the
mutated Character, reloads it through the repository, and requires Level 3 and
the increased maximum HP to survive that round trip.
