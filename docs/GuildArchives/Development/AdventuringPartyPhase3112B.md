# Phase III.11.2B — The Fellowship Standard

The Fellowship Hall now gives each Party a catalogue-backed visual identity.

## Hero containment correction

The III.11.2A company portrait combined a large minimum height with an aspect
ratio but did not explicitly constrain its width inside the Fellowship hero
grid.

Some browsers could therefore derive a wide portrait box from its height and
allow the company canvas to overlap the neighbouring Fellowship title and
actions.

III.11.2B explicitly constrains the hero portrait with:

- `min-width: 0`;
- `width: 100%`;
- `max-width: 100%`;
- local overflow containment;
- an isolated stacking context;
- `height: auto` on the company portrait;
- an independent Fellowship-copy stacking layer.

The taller III.11.2A company canvas remains intact.

## Fellowship Standard

Every Party now owns a `PartyStandard` value object containing:

- palette;
- emblem;
- ornament.

New Fellowships receive a deterministic Aubergine & Gold / Guild Star /
Guild Flourish standard.

The values come from controlled catalogues. Arbitrary CSS, markup, URLs or
free-form style values are not accepted.

## Initial palette catalogue

- Aubergine & Gold
- Pantry Green
- Frost Blue
- Berry Red
- Cheddar Gold

## Initial emblem catalogue

- Guild Star
- Market Leaf
- Company Crown
- Adventurers Cross
- Guild Cart

## Initial ornament catalogue

- Guild Flourish
- Laurels
- Three Stars
- Diamond
- Plain

## Persistence

The Standard is persisted independently in `_gmrc_party_standard`.

Older Fellowship records without Standard data safely hydrate with the
deterministic default.

Malformed stored Standard data also falls back to the default rather than
making the Fellowship unavailable.

## Application and HTTP

`UpdatePartyStandardAction` changes the Standard through the owner-scoped
Party application boundary.

`PUT /parties/{id}/standard` exposes that operation through the existing
nonce-safe Party form bridge.

The edit screen now provides controlled selectors and a Standard preview.

## Presentation

The Standard palette and emblem decorate:

- the open Fellowship Hall;
- Fellowship Register entries.

The existing Character/Fellowship portrait recipes remain untouched. The
Standard decorates the company around its adventurers rather than recolouring
or rewriting their authoritative portraits.
