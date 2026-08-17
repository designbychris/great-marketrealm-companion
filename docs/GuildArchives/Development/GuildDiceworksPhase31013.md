# Phase III.10.13 — Target-Aware Vital Application

Diceworks can now apply damage or healing only when the roll target is genuinely resolved.

## First actionable target: Self

The current Ledger can resolve Self because it owns the open Character record.

A resolved Self target must satisfy all of the following before Diceworks exposes an Apply action:

- target is marked resolved;
- target kind is `self`;
- target ID exactly matches the open Character ID;
- the Ledger's existing Vital Measures form is present.

This prevents a descriptive target label from becoming an accidental persistence authority.

## Existing Vital Measures remain authoritative

Diceworks does not duplicate the HP arithmetic.

When the player confirms an eligible result, Diceworks:

1. places the rolled total into the existing Quick Amount field;
2. invokes the existing Damage or Healing control;
3. lets `living-ledger.js` apply the established rules;
4. submits the existing nonce-protected Vital Measures form.

Therefore the existing rules remain authoritative:

- damage consumes Temporary HP first;
- remaining damage reduces Current HP;
- healing cannot exceed Maximum HP;
- Maximum HP remains read-only;
- the existing Character route persists the updated measures.

## Reference-only targets

Ally, Player Character, NPC and Hostile Creature remain non-mutating until a later party/encounter registry resolves them to concrete records.

A result against a reference-only target is still displayed and recorded in The Dice Ledger, but Diceworks explains that application is unavailable until encounter linkage exists.

This is the intended future seam for initiative-order combatants.

## Result immutability

Vital Application uses the target captured when the roll was made.

A natural-20 critical damage follow-up continues to inherit the original attack target and can expose application only if that inherited target is resolved.

D20 attack/check results never mutate vitality themselves. Damage/healing formula results and critical damage results are the actionable roll types.

## Boundary

III.10.13 does not create NPC HP stores, encounter combatants, party registries or initiative management.

Those future systems may provide additional resolved RollTargets without requiring Diceworks to own them.
