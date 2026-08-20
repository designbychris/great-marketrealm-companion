# Phase III.12.11E — The Cleric's Final Seal

III.12.11E certifies the complete Cleric Calling built across III.12.11
through III.12.11D.

## Certified Cleric stack

The Final Seal protects:

- specialist Cleric advancement;
- prepared Wisdom spellcasting;
- Level 1 Divine Domain selection;
- Sacred Domain Register;
- six Marketrealm Divine Domains;
- normalized 1 / 2 / 6 / 8 / 17 Domain Gift cadence;
- supplied Domain Spell tables;
- Channel Divinity progression;
- finite Domain Sacred Reserves;
- short-rest and long-rest ownership;
- Divine Arts;
- shared Guild Diceworks integration;
- `/devotion/*` route isolation from Paladin `/sacred/*`;
- responsive, reduced-motion and forced-colours-safe Ledger presentation.

## Domain consistency decisions

The Final Seal protects the normalization decisions made in III.12.11B.

### Dairy

Grease remains a 1st-level spell, not a cantrip.

Curdled Blessing remains a Level 2 Channel Divinity feature because Clerics
do not receive Channel Divinity until Level 2.

### Golden Arches

The supplied Domain Spell material remains intentionally partial rather than
receiving a fabricated full table.

### Editorial features

Features added to complete older sparse Domain drafts remain explicitly marked
as editorial in the gift catalogue.

## Channel Divinity

Channel Divinity remains one shared Cleric reserve:

- Level 2 — 1 use
- Level 6 — 2 uses
- Level 18 — 3 uses

It refreshes on a short or long rest.

Turn Undead and every Domain Channel Divinity technique spend that same pool.

## Rest ownership

A Cleric Sacred short rest restores short-rest Sacred resources such as
Channel Divinity but does not restore long-rest-only Domain resources.

`ClericSacredReserveService` restores Cleric Sacred resources on a long rest.

Shared spell slots remain separately owned by
`SharedSpellSlotReserveService`, preserving the same ownership boundary used
by the other spellcasting Callings.

## Source-fidelity protections

The Final Seal protects unusual or easy-to-break Domain mechanics including:

- Sweet Sanctuary temporary HP = Cleric level + Wisdom modifier;
- Divine Strike scaling from 1d8 to 2d8 at Cleric 14;
- Ferment Touch healing at 1d8 + Wisdom modifier;
- Ferment Touch enemy scaling at 1d8 / 2d8 / 3d8 / 4d8;
- Funk of the Divine damage at 2d10 + Cleric level;
- Mother Culture healing at 2d6 and damage at 4d6;
- Holy Butterstorm as separate 6d8 radiant and 2d8 fire damage.

Holy Butterstorm remains visibly prominent in the Ledger.

## Divine Intervention

No persistent Divine Intervention reserve is invented because the currently
certified source does not define a clean Companion expenditure cadence.

## Domain spell tables

Complete supplied spell tables remain certified for:

- Domain of Sweetness;
- Domain of Dairy;
- Domain of Seasoning;
- Domain of Cultivation;
- Domain of Fermentation.

Domain of the Golden Arches remains intentionally partial with only its
supplied signature spells.

## Diceworks

Divine Arts reuse the shared Guild Diceworks contract.

Formula, modifier, kind and damage type remain separate pieces of roll data.

No Cleric-specific dice engine exists.

## Route ownership

Cleric active-play expenditure continues to use:

- `/characters/{id}/devotion/spend`
- `/characters/{id}/devotion/rest`

The existing Paladin `/sacred/*` route namespace remains untouched.

## Browser hardening

The Final Seal reinforces:

- long-label wrapping;
- minimum action heights;
- very narrow-screen single-column layouts;
- reduced-motion compatibility;
- forced-colours button visibility.

The Cleric is therefore certified as a complete specialist Calling at the end
of III.12.11E.
