# Phase III.11.1A — Party Domain Foundation

Phase III.11 begins the transition from individual adventurers to an explicit Adventuring Party.

## Domain boundary

A Party is its own aggregate with:

- stable ULID identity;
- Fellowship name;
- administrative owner account;
- explicit Character memberships.

The Party contains references to Characters. It does **not** own Character entities.

Removing a Character from a Party therefore removes only the membership. It must never delete, rewrite or otherwise mutate that Character merely because the Fellowship changed.

## Party ownership

`PartyOwnerId` identifies the account responsible for administering the Party.

This ownership concept is intentionally separate from Party membership roles. A Character may be a Fellowship Leader without changing who owns/administers the Party record.

## Membership

`PartyMembership` references a `CharacterId` and carries a Party-facing role.

The initial sealed roles are:

- `leader`;
- `member`.

Duplicate membership of the same Character in one Party is rejected.

The role contract is deliberately small so later invitation, permissions, campaign and Dungeon Master work can extend application policy without changing Character ownership.

## Repository contract

`PartyRepositoryInterface` establishes the persistence seam for III.11.1B.

Owner-scoped reads are explicit:

- `allForOwner()`;
- `findForOwner()`.

Persistence implementation is intentionally absent from III.11.1A.

## Deferred work

This phase does not add:

- WordPress Party posts/meta;
- migrations;
- Party routes/controllers;
- invitations;
- UI;
- Guild Illuminator Party cards;
- initiative;
- encounters;
- DM permissions;
- shared HP mutation.

Those belong to later III.11 slices.

## Architectural rule

> A Party contains Characters. It does not own them.
