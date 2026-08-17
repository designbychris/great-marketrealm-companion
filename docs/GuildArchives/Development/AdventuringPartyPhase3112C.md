# Phase III.11.2C — The Company Charter

The Fellowship Hall now gives each Party a written identity alongside its
III.11.2B visual Standard.

## Charter model

`PartyCharter` contains three Fellowship-owned fields:

- motto — up to 90 characters;
- short company description — up to 240 characters;
- charter statement — up to 1,200 characters.

New Fellowships begin with a blank Charter.

Whitespace at the outer edge of each field is normalised, while intentional
line breaks inside the charter statement are preserved.

Control characters and over-limit values are rejected by the domain value
object.

## Persistence

The Charter is stored independently in `_gmrc_party_charter`.

Older Party records without Charter data hydrate with `PartyCharter::blank()`.

Malformed stored Charter data also falls back to a blank Charter rather than
making the Fellowship unavailable.

The Charter stores no Character data and does not alter membership records.

## Application and HTTP

`UpdatePartyCharterAction` updates the Charter through the same owner-scoped
Party application boundary used by the rest of the Fellowship Hall.

`PUT /parties/{id}/charter` exposes the operation through the existing
nonce-safe Party form bridge.

The request validates the same field lengths enforced by the domain object.

## Fellowship administration

Edit Fellowship now includes a Company Charter editor for:

- Fellowship motto;
- company description;
- charter statement.

All three fields are optional, so a Fellowship can remain visually defined
without being required to write lore immediately.

## Fellowship Hall presentation

A populated motto appears beneath the Fellowship name in the Hall hero.

When the Charter contains written content, the Hall gains a dedicated Company
Charter section decorated by the Fellowship Standard.

The short description is presented as the lead text and the longer charter
statement preserves paragraph breaks.

## Fellowship Register presentation

Register cards remain compact.

They show:

1. the Fellowship motto when one exists;
2. otherwise the short description;
3. otherwise no Charter copy.

The full charter statement is intentionally reserved for the opened
Fellowship Hall.
