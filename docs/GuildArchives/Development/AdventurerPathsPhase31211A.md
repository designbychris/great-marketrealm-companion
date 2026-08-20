# Phase III.12.11A — The Cleric's Sacred Domain Register

III.12.11A adds a Cleric-specific, read-only Sacred Domain Register to the
Spells & Abilities Ledger.

## Level 1 first

Cleric differs from several later-choice Callings because Divine Domain
selection belongs at Level 1.

A Level 1 Cleric therefore sees:

- prepared spellcasting already active;
- Wisdom spell save DC;
- Wisdom spell attack bonus;
- prepared-spell maximum;
- three baseline cantrips;
- first-circle shared spell slots;
- Divine Domain selection available immediately;
- Channel Divinity shown as opening at Level 2;
- the next sacred Calling milestone.

## Sacred calculations

Prepared spells remain:

`Cleric level + Wisdom modifier`, minimum one.

Channel Divinity display thresholds remain:

- 0 before Level 2;
- 1 use from Level 2;
- 2 uses from Level 6;
- 3 uses from Level 18.

III.12.11A displays those maxima only. Persistent expenditure belongs to the
later Sacred Reserves slice.

## Destroy Undead

The Register surfaces the Calling's threshold as progression advances:

- Level 5 — CR 1/2;
- Level 8 — CR 1;
- Level 11 — CR 2;
- Level 14 — CR 3;
- Level 17 — CR 4.

## Divine Domains

All six already bundled Great Marketrealm Divine Domains are visible.

The Register shows the certified catalogue label for a chosen Domain.

Domain Gifts remain explicitly pending III.12.11B rather than being inferred
from Domain names alone.

## Divine Intervention

Divine Intervention is displayed as an upcoming Level 10 feature and its final
Level 20 improvement is represented in the sacred progression state.

## Shared spell slots

The Register reads the existing ActiveClassResourceState through
SharedSpellSlotReserveService. It does not create a Cleric-specific duplicate
spell-slot ledger.

## Presentation

The Sacred Domain Register retains the Companion parchment language with a
subtle warm sacred-paper treatment.

It remains responsive, read-only, reduced-motion safe and forced-colours safe.
