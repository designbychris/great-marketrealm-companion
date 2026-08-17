# Phase III.11.1C — The Party Application Layer

The Party module now has explicit application use cases between presentation/controllers and persistence.

## Use cases

The application layer provides:

- Create Fellowship;
- list Fellowships for an owner;
- find one owner-scoped Fellowship;
- add an adventurer;
- remove an adventurer;
- change a membership role;
- rename a Fellowship;
- delete a Fellowship.

## Ownership boundary

Every operation that addresses an existing Party resolves it using both:

- Party ULID;
- Party owner ID.

A wrong owner therefore receives `PartyNotFound` rather than access to another account's Fellowship.

## Character membership boundary

Adding an adventurer requires the supplied Character ULID to resolve through `CharacterRepositoryInterface`.

The current Character repository is already scoped to the signed-in account, so arbitrary or foreign Character ULIDs cannot be added merely by knowing their identifier.

The Party application layer stores only the Character reference after successful resolution.

## Character independence

Removing an adventurer from a Fellowship removes only the Party membership.

No Party action invokes Character deletion. Character lifecycle remains wholly owned by the Characters module.

## Application exceptions

`PartyNotFound` represents an owner-scoped Fellowship lookup failure.

`PartyCharacterUnavailable` represents a Character that cannot be resolved for the current account when membership is requested.

These give the later HTTP/controller layer stable application failures to translate into user-facing responses.

## Deferred boundary

III.11.1C adds no HTTP routes, request parsing, forms, navigation or Fellowship Register UI.

Those arrive in III.11.1D and III.11.1E.
