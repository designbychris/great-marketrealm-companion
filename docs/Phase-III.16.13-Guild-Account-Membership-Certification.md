# Phase III.16.13 — Guild Account & Membership Certification

## Purpose

Guild Account & Membership Certification makes the signed-in Guild Profile the canonical account junction for Characters, Campaigns and Fellowships while protecting those live relationships from destructive Character deletion.

## Certified design

- The Guild Profile displays a live relationship certificate sourced from the existing repositories rather than duplicating relationship state.
- Character counts are owner-scoped to the signed-in Guild account.
- Player Campaign counts come from Player Campaign membership; Dungeon Master Campaign counts come only from Campaigns stewarded by that DM.
- Active and archived Campaign records remain visibly distinct.
- Fellowships are separated into owned and shared relationships through `SharedFellowshipAccess`, preserving custodianship boundaries.
- The certificate links back to the existing Character Register, Active Campaigns / Dungeon Master Campaign Register, and Fellowship Register.
- Player versus Dungeon Master calling remains immutable from Guild Profile editing.

## Character relationship protection

Before any portrait or Character persistence is deleted, `CharacterMembershipGuard` checks for live references.

Deletion is refused while the Character remains in:

- an active Campaign roster; or
- any Fellowship membership across Guild accounts.

The refusal is returned as a recoverable Guild error. The Player is sent back to the Adventurer Register and can release the relevant relationship before trying again.

Archived Campaign rosters remain historical and read-only, so an archived Campaign reference by itself does not block deletion. Any live Fellowship membership still does.

## Routing and security

The phase introduces no competing `admin-post.php` action family and no new role mutation path. Existing Guild Profile form routing, dedicated nonces, account capabilities and WordPress authentication boundaries remain unchanged.
