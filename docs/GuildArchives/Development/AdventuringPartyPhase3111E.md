# Phase III.11.1E — The Fellowship Register

The Party system is now visible as a first-class Companion experience.

## Fellowship navigation

The Parties Kingdom now contributes a `Fellowships` navigation entry using a dedicated Party icon.

This is the first phase in which the Party module is intentionally discoverable from the main Companion navigation.

## Fellowship Register

The temporary III.11.1D scaffold has been replaced with a Guild-ledger presentation.

Each Fellowship entry displays:

- Fellowship name;
- registered adventurer count;
- current Fellowship Leader when one is appointed;
- a composed Fellowship portrait;
- a direct Open Fellowship action.

The empty Register provides a Guild-styled call to form the first Fellowship.

## Fellowship portrait composition

Fellowship artwork is composed from the members' existing persisted Character portraits.

`FellowshipPresenter` resolves Party membership Character IDs against `CharacterRepositoryInterface` and then delegates portrait resolution to the established `PortraitRenderer`.

No Party-specific portrait generator, recipe or SVG renderer is introduced.

This preserves one source of truth for each adventurer's Guild illumination.

The composition supports both:

- generated SVG portraits;
- persisted custom media portraits.

Leader portraits receive a visual Guild seal. Large Fellowships show a bounded company composition with an overflow count.

The component is reusable for future campaign, initiative and encounter surfaces.

## Open Fellowship

Opening a Fellowship now presents:

- the larger company portrait;
- Fellowship name and company size;
- Auby company commentary;
- the Fellowship roster;
- individual adventurer portrait cards;
- Race, Class and Level summaries;
- Fellowship Leader distinction;
- Open Ledger links;
- role management;
- membership removal;
- Add Adventurer controls.

Missing/stale Character references remain representable rather than crashing the Fellowship.

## Forms

Create and Edit Fellowship views receive the same Marketrealm presentation language and retain the nonce-safe III.11.1D HTTP contracts.

Disbanding a Fellowship explicitly explains that Character records remain independent.

## Accessibility and resilience

The Fellowship portrait has an accessible composite label while its individual visual layers are decorative inside that composition.

Individual roster portraits receive explicit adventurer labels.

Focus-visible styles, responsive layout, reduced-motion-safe transitions and forced-colours support are included.

## Architectural rule retained

> A Party contains Characters. It does not own them.

The Fellowship Register resolves Character identity and portrait state only at presentation time.

## Future portrait direction

III.11.1E establishes **composed Fellowship portraits**, not a new persisted Party image.

A later phase may add portrait arrangement controls, banners, backgrounds, frames or an exportable company illustration while continuing to reuse the members' authoritative Character portraits.
