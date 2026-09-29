# Phase III.M.6E — The Adventurers Enter the Pocket

The authenticated native shell now consumes the existing owner-scoped `GET /gmrc-pocket/v1/characters` contract.

## Scope
- Adds a native Adventurers' Register after successful authentication.
- Shows each owned character's persisted portrait, name, level, race, class and live HP summary.
- Selecting an adventurer opens the first read-only native ledger: portrait, identity, HP, AC, initiative, speed, proficiency and ability scores.
- Refresh explicitly re-fetches the authoritative Pocket API.
- No native character database or character-data persistence is introduced.
- No rules/mechanics are duplicated in JavaScript.
- Existing bearer authentication and secure token storage are reused.
- 401/403 clears the stale secure key and returns to Auby's Guild Gate.

This phase intentionally does not add HP writes, combat controls, equipment, skills or spellbook native UI. Those can be layered onto the proven native character-selection boundary in subsequent phases.
