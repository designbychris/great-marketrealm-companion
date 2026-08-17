# Phase III.10.12d — Target Paint Runtime Correction

The hardened lifecycle regression finally exposed the actual runtime mismatch:

- `performFormula()` painted the selected target twice;
- `performD20()` did not paint the selected target at all.

The previous corrective patches were changing regression expectations around that mismatch rather than correcting both function blocks directly.

This patch makes the runtime invariant explicit:

- formula rolls paint their target exactly once;
- d20 rolls paint their target exactly once;
- critical damage continues to paint the target inherited from the original attack.

The target selector listeners remain bound exactly once outside `resetSituational()`.

No HP or vitality mutation is introduced.
