# Phase III.11.3C.2 — Stale Layout Regression Alignment

The first C.2 PHPUnit run produced four failures from
`CharacterLedgerLayoutBoundaryRegressionTest`.

## Cause

III.11.3C.1 introduced a temporary Character Ledger CSS boundary while the
browser symptom was still believed to be a layout containment problem.

III.11.3C.2 identified the actual cause as a runtime render interruption and
therefore intentionally removed that temporary boundary.

The C.2 patch package removed the obsolete test from its working tree.
However, extracting a ZIP over an existing Git checkout cannot delete files
that are no longer present in the ZIP.

The old regression consequently remained in the live repository and continued
asserting that the retired workaround must exist.

## Alignment

The existing regression file is now overwritten in place.

It protects the correct post-C.2 contract:

- the temporary Character Ledger boundary is absent;
- the temporary footer/float workaround is absent;
- the native Living Ledger root remains intact;
- native tab hiding remains intact;
- Character view block markup remains balanced;
- Fellowship CSS does not own the Character Ledger shell;
- the actual Fellowship role runtime contract remains available.

No production PHP, Character data, Fellowship data, Purse data, Treasury data,
Coin Between Companions logic, or CSS is changed by this alignment.
