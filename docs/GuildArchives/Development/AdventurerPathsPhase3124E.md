# Phase III.12.4E — The Rogue's Final Seal

III.12.4E hardens the complete Rogue implementation and certifies Rogue as
GMRC's reference Calling for contextual, every-turn and once-per-turn play.

No new Rogue feature is introduced by this phase.

## Certified Rogue stack

The Final Seal protects:

- specialist Rogue progression;
- Rogue Archetype selection at Level 3;
- six Great Marketrealm Rogue Archetypes;
- Archetype Gifts at Levels 3 / 9 / 13 / 17;
- pre-choice identity, playstyle and best-for guidance;
- Cunning Register;
- Cunning Action;
- Dash / Disengage declarations;
- Hide through the real Dexterity (Stealth) check;
- Sneak Attack scaling;
- once-per-turn local Sneak Attack record;
- Uncanny Dodge declaration at Level 5;
- Evasion guidance at Level 7;
- Guild Diceworks integration;
- responsive and accessible Ledger presentation.

## Sneak Attack single authority

Earlier Rogue slices derived Sneak Attack scaling in both the Cunning Register
and Precision & Reaction presenter.

The Final Seal removes that duplication.

`RoguePrecisionPolicy::sneakAttackDice()` is now the single certified scaling
authority:

- Level 1–2: 1d6
- Level 3–4: 2d6
- Level 5–6: 3d6
- continuing every odd Rogue level
- Level 19–20: 10d6

This is a behavior-neutral cleanup.

## Cunning Action boundary

Cunning Action remains:

`Bonus action · Every turn`

It is not stored as a finite resource.

Dash and Disengage remain browser-local declarations with no fake rolls.

Hide continues to use the Character's real Stealth modifier and proficiency /
expertise state through Guild Diceworks.

## Precision boundary

Sneak Attack remains once per turn rather than once per rest.

Its ready/used record is therefore browser-local and explicitly reset through
**Start New Turn**.

The Companion still refuses to decide whether a battlefield situation
qualifies for Sneak Attack.

## Reaction boundary

Uncanny Dodge unlocks at Level 5 and records a browser-local reaction
declaration.

The Companion does not infer:

- attacker visibility;
- whether the hit qualifies;
- final reduced damage.

Evasion unlocks at Level 7 and remains passive guidance with no invented use
counter.

## Archetype boundary

All six Rogue Archetypes retain the certified cadence:

`3 → 9 → 13 → 17`

Only persisted Guild-Certified Archetype Gifts appear in the Cunning Register.

## Class isolation

Rogue-specific Register, Cunning Action and Precision/Reaction presenters do
not leak to Fighter or other Callings.

## Reference implementation status

Once PHPUnit-green, Rogue becomes GMRC's reference implementation for
contextual active-play mechanics that are not traditional resource pools:

- repeatable every-turn declarations;
- scene-dependent skill rolls;
- once-per-turn damage;
- reaction declarations;
- passive resolution guidance.

The four mature reference Calling families are now:

- Wizard — spellcasting progression
- Fighter — martial expendable resources
- Barbarian — persistent active state + limited resources
- Rogue — contextual every-turn / once-per-turn / reaction play
