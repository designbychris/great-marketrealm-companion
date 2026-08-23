# Phase III.16.5 — The Canonical Calling Register

The Steward's Office now exposes the Players Handbook-backed class and subclass catalogue as a searchable Canonical Calling Register.

## Contract

- `resources/catalogue/players-handbook.v1.json` remains the bundled canonical baseline.
- Steward changes are stored separately in `gmrc_canonical_calling_overrides`.
- Name/description wording and private Steward notes may be overridden.
- Class hit dice and subclass parentage are deliberately read-only in this first administration pass.
- Existing Character identity, advancement history, progression definitions and Path Gift machinery are not rewritten.
- Every write requires `manage_options` and a record-specific nonce.
- Restore removes the overlay and reveals the Players Handbook baseline again.

This is intentionally the safe administration foundation. A later Calling-mechanics phase can route certified player-facing readers through an override-aware service once feature-level editing has its own validation and historical-snapshot policy.
