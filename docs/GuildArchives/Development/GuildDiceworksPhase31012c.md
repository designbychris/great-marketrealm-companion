# Phase III.10.12c — Target Paint Test Hardening

The previous target-paint regression searched the entire Diceworks source for a repeated text fragment. That check was brittle and produced a false failure even though the runtime target painting was correct.

The regression now scopes its assertions to the actual roll functions:

- `performFormula()` must paint the selected target exactly once;
- `performD20()` must paint the selected target exactly once;
- `performCriticalDamage()` must continue to paint the inherited critical target.

No runtime Diceworks code changes in this correction.
