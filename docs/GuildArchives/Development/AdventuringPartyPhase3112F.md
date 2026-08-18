# Phase III.11.2F — The Company Chronicle

The Fellowship Hall now owns a persistent collective history.

## Chronicle entry types

The Chronicle domain recognises three entry types from its first phase:

- Adventure Note
- Company Deed
- Fellowship Honour

Adventure Notes are ordinary Fellowship-authored records.

Company Deeds and Fellowship Honours are formal records that require Dungeon Master certification.

## Provenance and certification

Every Chronicle entry records:

- ULID entry identity;
- entry type;
- title;
- content;
- provenance;
- author user ID;
- certification state;
- recorded timestamp.

Player-written Adventure Notes use `player` provenance and are never certified.

Company Deeds and Fellowship Honours can only be constructed through the certified record boundary, which requires `dungeon-master` provenance and a certified state.

III.11.2F deliberately does **not** expose a player-facing HTTP route for certified records.

The future Dungeon Master system can consume the existing domain contract rather than retrofitting certification later.

## Persistence

Chronicle entries are stored independently in `_gmrc_party_chronicle`.

Older Fellowships without Chronicle data hydrate with an empty Chronicle and require no migration.

Malformed stored entries are skipped rather than making the Fellowship unavailable.

## Player application flow

The current player-facing feature is Adventure Notes.

`AddPartyChronicleNoteAction` works through the owner-scoped Party application boundary.

HTTP route:

`POST /parties/{id}/chronicle/notes`

Nonce contract:

`gmrc_party_chronicle_{partyId}`

The request requires:

- title up to 120 characters;
- content up to 3,000 characters.

## Fellowship Hall

The Hall now includes a Company Chronicle with:

- Auby's Guild Historian note;
- Adventure Note writing form;
- persistent chronological timeline;
- newest entries first;
- type glyph and label;
- entry date;
- provenance display;
- visual support for future DM-certified records;
- empty-state presentation.

The Hall clearly tells players that their Adventure Notes are Fellowship records rather than Dungeon Master-certified Deeds or Honours.

## Architectural boundary

The Chronicle belongs to the Fellowship.

It does not alter:

- Character records;
- Character Notes;
- Fellowship Treasury;
- Character inventory;
- Diceworks;
- Campaign or encounter state.

Future systems may contribute formal Chronicle entries without owning the Chronicle itself.

## Future DM awards

A later Dungeon Master chapter can issue:

- certified Company Deeds;
- Fellowship Honours;
- medals, commendations and titles.

Those awards can then be rendered by the Chronicle and may later decorate the Fellowship Standard or Company Portrait.

## Related roadmap follow-ups

Separate follow-up work has been captured for:

- showing a Character's Fellowship memberships from the Character Ledger;
- adding personal Character currency tracking;
- transactional transfers between Character currency and Fellowship Treasury.
