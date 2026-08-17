# Phase III.10.15 — Diceworks Hardening & Seal

Phase III.10 closes with a defensive hardening pass and chapter-wide regression seal.

## Runtime hardening

### Authored formula validation

Diceworks continues to accept the supported formula families:

- d4
- d6
- d8
- d10
- d12
- d20
- d100

Authored formulas must now contain between 1 and 20 base dice. Invalid formula data such as `0d6`, unsupported die families, or formula counts above the authored-roll limit return no roll instead of being silently rewritten.

A critical workflow may still produce a larger visual pool by doubling a valid authored weapon formula.

### Bounded numeric input

A shared bounded-integer helper now protects free-roll quantity and modifier values from non-finite or out-of-range input.

Free rolls remain capped at 20 dice and modifiers remain bounded to the UI contract of -99 through +99.

The same rules are used when a Free Roll is stored as a Quick Roll favourite.

### Roll-mode fallback

Unknown d20 mode data now falls back to Normal rather than accidentally behaving as Disadvantage.

The three supported modes remain Normal, Advantage and Disadvantage.

## Regression sweep

The seal protects the complete III.10 surface:

- secure d4–d100 rolling;
- quantities and huge pools;
- Normal / Advantage / Disadvantage;
- natural 20 and natural 1 reactions;
- the lonely Nat1 confetti;
- damage and healing formulas;
- character-aware modifiers;
- Dice Ledger history;
- Quick Rolls and unavailable favourites;
- Situational Adjustments;
- critical damage;
- PHP-authoritative spell and ability scaling;
- the Indexed Arcane Pantry;
- target contracts;
- critical target inheritance;
- resolved-Self Vital Application;
- reference-only encounter targets;
- keyboard focus;
- screen-reader announcements;
- reduced motion;
- mobile Diceworks;
- forced-colours/high-contrast behaviour.

## Encounter boundary

Diceworks is deliberately not an encounter manager.

Hostile Creature, NPC, Ally and other reference-only targets remain non-mutating until a future party/initiative/encounter system supplies a concrete target ID and vitality authority.

That future integration should provide resolved RollTargets to Diceworks rather than teaching Diceworks how encounters are stored.

## Seal

With this phase green, Phase III.10 — The Guild Diceworks is considered architecturally sealed.

Future work may extend Diceworks through explicit contracts, but the III.10 rules, safety boundaries and accessibility guarantees are protected by regression coverage.
