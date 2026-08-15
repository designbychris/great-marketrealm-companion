# Phase III.10.7 — The Dice Ledger

Guild Diceworks now keeps a character-scoped record of recent rolls for the current browser session.

## Session-aware history

The previous six-entry in-memory list has become a twelve-entry Dice Ledger. History is stored in `sessionStorage`, keyed by Character ID, so:

- refreshing the Ledger does not immediately lose the rolls;
- opening another adventurer does not mix their rolls into this Ledger;
- closing the browser session naturally retires the temporary record;
- blocked/unavailable browser storage never prevents Diceworks from rolling.

This is intentionally session persistence rather than permanent Character-domain history. Dice results are play-session ephemera and are not added to the Living Register or CharacterRepository.

## Structured entries

Each remembered roll can retain:

- readable result text;
- roll kind;
- formula;
- individual dice;
- modifier;
- total;
- natural d20 result when relevant;
- Nat 20/Nat 1 reaction state;
- local roll time.

The visible Ledger remains concise while the structured shape prepares later Diceworks phases.

## Player controls

The history panel is now titled **The Dice Ledger** and includes **Clear Ledger**.

Clearing removes only the current adventurer's Dice Ledger for this browser session. It does not change character state, HP, spells, equipment, progression, or the Living Register.

## Boundary

Phase III.10.7 does not introduce favourites, situational modifiers, critical-damage follow-through, targeting, or permanent campaign logs. Those remain later III.10 phases.
