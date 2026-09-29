# Phase III.M.6F — The Adventurer Takes the Field

The native Pocket character proof becomes the first usable native dashboard.

## Native dashboard
- Establishes the five-destination shell: Overview, Character, Combat, Spellbook, More.
- Overview owns the first live native feature: Adventurer's Vitality.
- Character exposes the six canonical ability scores.
- Combat, Spellbook and More are intentionally honest placeholders for later phases.

## Adventurer's Vitality
- Current HP and Temporary HP are editable.
- Maximum HP is presented as a certified read-only measure.
- Direct saves use the existing owner-scoped vitality endpoint with optimistic concurrency.
- Damage and healing use the same endpoint, but the arithmetic is now resolved server-side for native clients.
- Damage consumes Temporary HP before Current HP.
- Healing cannot exceed Maximum HP.
- 401/403 clears the native secure key; stale writes are rejected by the server.
- No character data is persisted in native storage and no offline writes are introduced.

## Fellowship Auby polish
All Fellowship Auby-note seals now use the existing canonical `auby-note-face.svg` artwork instead of the retired standalone aubergine emoji. Quote wording is unchanged.
