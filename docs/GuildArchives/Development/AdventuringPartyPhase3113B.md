# Phase III.11.3B — The Adventurer's Purse

Phase III.11.3 now gives individual adventurers their own personal currency
record.

## Personal versus shared coin

The financial boundary is explicit:

- **Adventurer's Purse** — personal Character funds.
- **Fellowship Treasury** — shared company funds.

III.11.3B does not transfer money between these records. That transactional
bridge is reserved for III.11.3C.

## Canonical money

`CharacterPurse` stores personal wealth canonically as copper pieces.

The Ledger presents and accepts:

- Gold Pieces (GP);
- Silver Pieces (SP);
- Copper Pieces (CP).

The conversion remains:

- 1 GP = 10 SP;
- 1 GP = 100 CP.

A purse can never contain a negative amount.

Deposits and withdrawals must contain at least one copper piece.

A withdrawal that exceeds the personal balance is rejected.

## Persistence

Character purse balance is stored in:

`_gmrc_character_purse_copper`

Existing Characters without this metadata hydrate with an empty purse.

No migration is required.

The purse is part of the Character aggregate so future Character ↔ Fellowship
transactions can mutate a domain-owned balance rather than manipulating raw
WordPress metadata.

## Application routes

Personal coin uses dedicated routes:

- `POST /characters/{id}/purse/deposit`
- `POST /characters/{id}/purse/withdraw`

The existing owner-scoped Character repository remains the authority for
finding and saving the Character.

Nonce contract:

`gmrc_character_purse_{characterId}`

## Validation

Each request accepts:

- GP: 0–999,999;
- SP: 0–9;
- CP: 0–9.

All-zero requests are rejected.

Withdrawals are checked against the current canonical balance before the
Character is saved.

## Character Ledger

The Equipment tab now includes **The Adventurer's Purse** alongside the
Adventurer's Pack.

It provides:

- current GP/SP/CP balance;
- Add Coin controls;
- Spend Coin controls;
- clear copy distinguishing personal funds from Fellowship funds;
- responsive and high-contrast-aware presentation.

## III.11.3C preparation

The purse deliberately exposes canonical deposit and withdrawal domain
operations.

III.11.3C can therefore implement **Coin Between Companions** as a true
Character ↔ Fellowship Treasury transaction:

1. validate Character membership and ownership;
2. validate sufficient source funds;
3. debit one side;
4. credit the other;
5. persist both records;
6. record transfer provenance in the Fellowship Treasury ledger;
7. fail safely rather than silently duplicating or destroying coin.

III.11.3B itself performs no Fellowship Treasury mutation.
