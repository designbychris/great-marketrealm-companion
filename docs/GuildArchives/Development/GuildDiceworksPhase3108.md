# Phase III.10.8 — Roll Favourites & Quick Rolls

Guild Diceworks now supports persistent, character-scoped favourites and one-click Quick Rolls.

## Persistent favourites

Unlike the session-only Dice Ledger, Quick Roll favourites use browser `localStorage` and are keyed by Character ID. They therefore survive browser restarts while remaining isolated between adventurers.

Up to eight favourites may be pinned per character.

## Character-aware favourites

Character-derived rolls do not store a copied modifier. A favourite stores a stable reference built from the existing roll context and resolves the current Ledger trigger when used.

This means progression remains authoritative. If a skill, save, attack or spell modifier changes later, the Quick Roll reads the current `data-roll-modifier` from the Ledger instead of preserving stale arithmetic.

If a previously pinned character roll is no longer present, it remains visible as unavailable and can be removed.

## Free Roll favourites

A designed Guild Free Roll is different: its chosen quantity, die type and modifier are the definition itself. Free Roll favourites therefore persist:

- quantity;
- die sides;
- modifier;
- formula label.

Selecting that Quick Roll restores those controls and rolls it immediately.

## Player experience

The Open Ledger toolbar now provides a **Quick Rolls** launcher.

Within Diceworks, a character roll can be toggled into or out of Quick Rolls using **Add to Quick Rolls / Remove from Quick Rolls**. The Guild Free Roll area includes **Save as Quick Roll**.

Each Quick Roll has its own remove control and the tray reports the current favourite count.

## Boundaries

Favourites are browser preferences, not canonical Character state. They are not written to `CharacterRepository`, progression, the Living Register, or the Dice Ledger.

Phase III.10.8 does not yet add situational modifiers or critical-damage follow-through.
