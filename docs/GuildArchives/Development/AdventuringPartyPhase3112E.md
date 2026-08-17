# Phase III.11.2E — The Fellowship Treasury

The Fellowship Hall now owns a shared company purse and an auditable Treasury Ledger.

## Canonical money

Fellowship funds are stored canonically as copper pieces through `PartyTreasuryMoney`.

The UI accepts Gold, Silver and Copper using the standard 1 GP = 10 SP = 100 CP relationship.

A Treasury balance can never be negative.

## Transactions

Deposits and withdrawals create immutable `PartyTreasuryTransaction` records containing:

- ULID transaction identity;
- deposit/withdrawal type;
- canonical amount;
- optional note up to 160 characters;
- transaction timestamp.

Zero-value transactions are rejected.

Withdrawals that exceed the available Treasury balance are rejected before the balance or ledger changes.

## Persistence

Treasury data is stored separately in `_gmrc_party_treasury`.

The persisted record contains the canonical copper balance and transaction history.

Older Fellowships without Treasury data hydrate with an empty Treasury, requiring no migration.

Malformed Treasury data fails safely to an empty Treasury.

## Application and HTTP

Owner-scoped application actions provide:

- `DepositPartyTreasuryAction`
- `WithdrawPartyTreasuryAction`

HTTP routes:

- `POST /parties/{id}/treasury/deposit`
- `POST /parties/{id}/treasury/withdraw`

Both use dedicated authenticated Form Requests and the nonce contract `gmrc_party_treasury_{partyId}`.

## Fellowship Hall

The Hall now displays:

- current company purse;
- Deposit Funds form;
- Withdraw Funds form;
- optional transaction notes;
- six most recent Treasury transactions;
- Quartermaster name when that Company Office is appointed.

The Quartermaster is informational in III.11.2E and is **not** a permission gate.

## Architectural boundary

The Fellowship Treasury is independent from:

- Character inventory;
- Character personal currency;
- Character HP/Vital Measures;
- Diceworks targeting.

No Character record is mutated by Treasury operations.

A later shared inventory/loot system may consume the Treasury contract without coupling company funds to individual adventurer packs.
