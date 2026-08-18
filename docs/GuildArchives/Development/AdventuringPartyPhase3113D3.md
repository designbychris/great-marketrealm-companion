# Phase III.11.3D.3 — Connected Coin Flow

Browser testing confirmed that ordinary Fellowship Treasury deposits and
withdrawals were functioning, but they were being mistaken for Character
transfers.

## Two distinct financial workflows

The Fellowship Treasury intentionally supports two different operations.

### Coin Between Companions

This is the connected Character ↔ Fellowship workflow.

It:

- selects a Fellowship member;
- displays that Character's current personal purse;
- moves an exact amount from one balance to the other;
- updates both the Character Purse and Fellowship Treasury;
- records Character and direction provenance in the Treasury Ledger.

This is now the primary Treasury workflow.

### Company-only Treasury adjustments

These represent money entering or leaving the Fellowship without coming from
a Character's personal purse, such as:

- contract rewards paid directly to the company;
- treasure sold by the Fellowship;
- shared supplies purchased from company funds;
- fees, repairs or other collective expenses.

These controls deliberately do not change a Character's purse.

They are now renamed to **Record External Income** and
**Record Company Expense**, placed inside a collapsed
**Company-only Treasury adjustments** disclosure, and accompanied by an
explicit explanation.

## Why this matters

A generic button labelled `Deposit Funds` implied that an adventurer was
depositing personal money.

That was technically incorrect and made two separate accounting paths look
like one broken connected system.

The revised UI makes the transactional Character bridge the obvious default
while preserving legitimate company-only accounting.

No money-domain behaviour changes are introduced in D.3. The connected
transfer action from III.11.3C/D remains the authority for movements between
the two balances.
