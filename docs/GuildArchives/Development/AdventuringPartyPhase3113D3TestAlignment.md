# Phase III.11.3D.3 — Connected Coin Flow Test Alignment

The first D.3 PHPUnit run produced three presentation-regression failures.

## Cause

All three failures were stale expectations after the Treasury UI wording was
intentionally clarified.

### Connected Coin Flow regression

The test searched for the shortened literal route:

`'/treasury/transfer'`

The registered route is correctly:

`'/parties/{id}/treasury/transfer'`

### Coin Between Companions regression

The connected transfer button was intentionally renamed from:

`Transfer Coin`

to:

`Move Coin Between Purses`

to make the two-balance behaviour explicit.

### Fellowship Treasury regression

The ordinary Treasury controls were intentionally renamed from:

- `Deposit Funds`
- `Withdraw Funds`

to:

- `Record External Income`
- `Record Company Expense`

These controls remain company-only accounting and do not mutate a Character's
personal purse.

## Alignment

The existing regressions now protect the new sealed wording and full route
contract.

No production PHP, routing, purse, Treasury, transfer, portrait, CSS or
Character Ledger behaviour changes are introduced by this alignment.
