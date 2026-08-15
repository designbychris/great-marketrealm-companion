# Phase III.10.3 — Character-Aware Diceworks

The Guild Diceworks now carries structured context from the Character Ledger into every character-driven roll.

## Roll context

Character-facing roll triggers can provide:

- roll kind;
- source;
- governing ability;
- training state (untrained, proficient, or expertise);
- the already-resolved modifier.

The Diceworks tray presents this context before the result and recent-roll history preserves it with the arithmetic.

## Authoritative rules boundary

PHP and the Character domain remain responsible for ability modifiers, proficiency, expertise, attack bonuses, saving throws, skill modifiers and spell attack values. JavaScript does not recalculate those rules. It receives the resolved modifier and descriptive context, then performs the random roll and presentation only.

## Supported contexts

The current Ledger now identifies:

- Initiative — Dexterity;
- Ability Checks — governing ability;
- Saving Throws — ability and proficiency;
- Skill Checks — skill, governing ability and proficiency/expertise;
- Weapon Attacks and Damage — weapon source and attack ability;
- Spell Attacks and formula rolls — spell/ability source and casting ability.

## Free rolls

Guild Free Roll remains intentionally character-neutral. It is labelled as a Free Roll and does not pretend to have an ability or proficiency source.

## Boundaries

No Character state, HP, progression, certification or Living Register data is mutated by this phase.
