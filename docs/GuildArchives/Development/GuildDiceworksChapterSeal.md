# Phase III.10 — The Guild Diceworks

## Chapter Seal

The Guild Diceworks chapter transforms the Character Ledger from a static record into an active tabletop companion.

The sealed system now covers the complete roll lifecycle:

**Character rule → Diceworks context → secure roll → visible result → Dice Ledger record → optional resolved-target application.**

## Sealed capabilities

### Core dice engine

Diceworks supports d4, d6, d8, d10, d12, d20 and d100 with secure browser randomness where available and a safe fallback.

Free Rolls support up to twenty dice, negative or positive modifiers, and reusable Quick Roll favourites.

### Character-aware rolling

Ability checks, saving throws, attacks, damage, healing, spells and class features can carry authoritative Character context into Diceworks.

Situational Adjustments remain explicitly temporary and reset after the next roll.

### Natural reactions and criticals

Natural 20 and Natural 1 results have persistent textual meaning as well as visual reactions.

A weapon-attack Natural 20 can launch the PHP-resolved critical-damage formula while preserving the original target.

Auby's critical guidance and famously lonely Natural-1 confetti remain part of the sealed presentation.

### Arcane scaling

Character-level, spell-slot and future feature-rank scaling are resolved in PHP.

Diceworks consumes the resolved formula and does not duplicate progression rules.

The Arcane Pantry has sufficient Wizard stock through the first three spell levels and the live Ledger hides spell circles the adventurer cannot yet cast.

### Targeting

The target contract supports:

- Self
- Ally
- Player Character
- NPC
- Hostile Creature

Self is currently a resolved Character target. Other categories may be recorded as reference-only targets.

A future encounter and initiative system can turn those reference categories into resolved combatants by supplying concrete target records through the same contract.

### Vital Application

Resolved Self damage/healing may reuse the existing Adventuring Measures workflow.

The established vitality rules remain authoritative: Temporary HP absorbs damage first, healing cannot exceed Maximum HP, and Maximum HP remains Guild-certified.

Reference-only targets cannot mutate vitality.

### Accessibility and resilience

The sealed Diceworks includes:

- keyboard-aware focus movement;
- screen-reader result announcements;
- textual Nat20/Nat1 meaning;
- reduced-motion support;
- large-pool presentation;
- mobile tray containment;
- high-contrast/forced-colours support;
- bounded malformed-input handling;
- graceful unavailable Quick Rolls and blocked storage behaviour.

## Future encounter integration

The intended future architecture is:

**Diceworks rolls.  
The Targeting Contract identifies.  
The Encounter system owns combatants.  
Vital Application changes the resolved target.**

When the Dungeon Master places a Gravy Golem, Rotling or other creature into a future initiative order, that encounter system should expose it as a resolved Hostile Creature target.

Diceworks should not need to know whether that creature came from initiative, a campaign encounter, an NPC library or another combat source.

## Final Phase

Phase III.10.15 — Diceworks Hardening & Seal closes this chapter.

Any later Diceworks additions should preserve the contracts established here or explicitly version them rather than silently weakening the sealed behaviour.
