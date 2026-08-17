# Phase III.11.1B — Party Persistence

The Fellowship now has a WordPress-backed permanent record.

## Storage

Parties are stored as private `gmrc_party` posts.

The WordPress author field stores the Party administrative owner. Domain identity remains the Party ULID stored in `_gmrc_party_id`.

Memberships are stored separately in `_gmrc_party_memberships` as Character references and Party roles.

## Ownership safety

Repository reads and deletes require both:

- Party ID;
- Party owner ID.

This prevents one account from resolving or deleting another account's Fellowship merely by knowing its Party ULID.

Saving uses the immutable Party owner from the domain aggregate.

## Membership persistence

Stored membership rows contain only:

- Character ULID;
- Party-facing role.

The repository does not copy Character data into the Party record and does not mutate Characters during save/delete.

Malformed membership rows, unknown roles and duplicate stored membership rows are skipped safely during hydration.

A valid but no-longer-existing Character reference remains a membership reference; stale-Character resolution belongs to the later application/presentation layer.

## Duplicate Party records

If WordPress contains more than one owner-scoped record for the same Party ULID, the repository fails closed with an exception instead of selecting an ambiguous record.

## Module registration

A new `PartiesKingdom` registers `PartiesServiceProvider`, which:

- binds the concrete Party repository;
- resolves `PartyRepositoryInterface` through the same singleton;
- registers the private `gmrc_party` post type.

No Party navigation, routes or UI are exposed yet.

## Next

Phase III.11.1C will add the Party application layer: creation, membership management and ownership-aware use cases.
