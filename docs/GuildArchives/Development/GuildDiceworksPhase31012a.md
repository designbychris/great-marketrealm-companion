# Phase III.10.12a — Targeting Contract Regression Fix

The first III.10.12 PHPUnit run exposed one stale III.10.10 regression assertion and review of the captured JavaScript exposed one listener-lifecycle placement issue.

## Critical follow-up regression

III.10.10 originally defined:

`prepareCriticalFollowUp(selection)`

III.10.12 intentionally extended the contract to:

`prepareCriticalFollowUp(selection, target)`

so a pending natural-20 critical damage roll retains the target captured by the original attack.

The older regression now expects the target-aware contract rather than the superseded signature.

## Target listener lifecycle

The target kind/name event listeners had accidentally been inserted inside `resetSituational()`.

Although syntactically valid, this would bind another listener each time a situational modifier reset after a roll.

The listeners are now bound once during Diceworks initialisation, alongside the other tray controls.

## Target result painting

Formula rolls and d20 rolls each paint their selected target once. A duplicate formula target paint was removed and the d20 path explicitly paints its target before revealing the result.

No target or HP mutation rules changed in this correction.
