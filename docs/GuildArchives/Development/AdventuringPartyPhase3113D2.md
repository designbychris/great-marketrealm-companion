# Phase III.11.3D.2 — Treasury Validated Input Repair

Browser testing after III.11.3D.1 reached the Treasury controller correctly
but exposed a request-accessor mismatch.

## Runtime failure

The Treasury request classes called:

`$this->validated()->int(...)`

The actual `ValidatedInput` API exposes:

`$this->validated()->integer(...)`

The route and admin-post dispatch were therefore functioning correctly. The
fatal occurred only after the request had been validated and the controller
asked the request for GP/SP/CP values.

## Repair

The following requests now use the supported `integer()` accessor:

- `DepositPartyTreasuryRequest`
- `WithdrawPartyTreasuryRequest`
- `TransferPartyCoinRequest`

String accessors remain unchanged.

## Regression coverage

The new regression verifies:

- `ValidatedInput::integer()` converts validated numeric values;
- all three Treasury requests use `integer()` for GP/SP/CP;
- none of them call the nonexistent `int()` method;
- the core validation API itself remains aligned with those requests.

No routing, purse, Treasury, transfer, portrait or Character Ledger behaviour
changes are introduced by this repair.
