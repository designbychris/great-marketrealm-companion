# Phase III.10.12b — Target Paint Regression Correction

The III.10.12a lifecycle regression incorrectly expected three direct calls to:

`paintTargetResult(target);`

The Diceworks implementation correctly has two direct target paints:

- formula rolls;
- d20 rolls.

Critical damage intentionally uses:

`paintTargetResult(critical.target || null);`

because its target is inherited from the original natural-20 attack rather than read from the current selector.

The regression now asserts the correct two direct calls and separately protects the inherited critical-target path.

No runtime Diceworks behaviour changes in this correction.
