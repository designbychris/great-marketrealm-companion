# Phase III.11.3A — Fellowship Bonds

Phase III.11.3 begins the bridge between individual Character Ledgers and
Fellowships.

## Character Ledger Fellowship memberships

The Character Ledger's **Archive Notes** tab now contains a dedicated
**Fellowships** section.

For each Fellowship that contains the adventurer, the Ledger displays:

- Fellowship name;
- Fellowship Standard emblem/palette;
- membership role;
- Company Office when assigned;
- registered member count;
- direct link to the Fellowship Hall.

A Character may appear in more than one Fellowship.

Characters with no Fellowship membership receive a dedicated empty state.

## Read-only presentation bridge

`CharacterFellowshipPresenter` performs the cross-module lookup.

It receives:

- Character ID;
- current Party owner/account ID.

It then searches only the owner's Fellowships and returns those containing
that Character.

The bridge is deliberately read-only. Opening a Character Ledger cannot:

- add/remove Fellowship members;
- change leadership;
- change Company Offices;
- mutate the Character;
- mutate the Fellowship.

## Controller compatibility

The Character Controller receives the Fellowship presenter as an optional
final constructor dependency.

This preserves direct construction used by older controller unit tests while
allowing the application container to resolve the presenter in production.

## Why Archive Notes?

The Notes tab already represents the adventurer's wider Guild record and
future journal/archive relationships.

Fellowship membership fits naturally there without overcrowding the combat,
equipment, progression or identity pages.

## Phase III.11.3 roadmap

III.11.3 is the **Adventurer & Fellowship Bridge**.

Planned follow-on slices:

- **III.11.3A — Fellowship Bonds** — Character Ledger Fellowship links.
- **III.11.3B — The Adventurer's Purse** — personal Character currency.
- **III.11.3C — Coin Between Companions** — transactional Character ↔
  Fellowship Treasury transfers.
- **III.11.3D — Bridge Hardening & Seal** — consistency, permissions,
  transaction integrity, accessibility and regression sweep.

After this bridge chapter, the roadmap returns to the broader Character
class/progression completion pass so classes beyond Wizard receive the same
systematic depth where appropriate.
