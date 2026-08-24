# Phase III.16.13 — Guild Account & Membership Certification

Phase III.16.13 certifies the signed-in Guild account as the canonical junction between Characters, Campaigns and Fellowships.

## Certified account relationships

The Guild Profile now presents a live membership certificate showing:

- Characters owned by the signed-in Guild account.
- Active and archived Campaign relationships.
- Player Campaign membership versus Dungeon Master Campaign stewardship.
- Fellowships owned by the account.
- Fellowships shared through one of the account's Characters.

The certificate links back to the established Character Register, Active Campaigns or Dungeon Master Campaign Register, and Fellowship Register rather than introducing duplicate management surfaces.

## Character relationship protection

Character deletion now checks the live relationship registers before destructive work begins.

A Character cannot be deleted while its stable Character ID is still present in:

- an active Campaign roster; or
- any live Fellowship membership, including a shared or Campaign-founded Fellowship.

The guard runs before portrait cleanup and before the Character repository delete, preventing partial deletion. The Character controller catches the relationship refusal and returns the Guild member to the Adventurer Register with a recoverable error explaining what must be released first.

Archived Campaigns remain immutable historical records. Their roster references do not independently block Character deletion; however, any live Fellowship relationship still does. This preserves Campaign history without allowing an active Fellowship access relationship to become dangling.

## Authorization boundaries

Phase III.16.13 does not add any role-changing controls. Player versus Dungeon Master calling remains capability-protected and cannot be changed through the Guild Profile.

Player account summaries resolve Campaigns through the Player Campaign membership repository. Dungeon Master summaries resolve only Campaigns owned by that DM. Fellowship visibility continues through `SharedFellowshipAccess`, keeping ownership distinct from shared membership.

## Routing protection

No new WordPress `admin-post.php` action family was introduced. Existing Guild Profile forms continue through the certified Companion `gmrc_app_request` gateway with their dedicated routes and nonces, preserving the Phase III.16 front-end routing hotfixes.

## Certification baseline

Built on the Phase III.16.12 certified baseline:

- 3,637 tests
- 13,150 assertions
- ALL GREEN

Phase III.16.13 adds dedicated Guild account and membership certification coverage before the next PHPUnit certification run.
