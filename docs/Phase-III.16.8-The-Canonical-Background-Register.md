# Phase III.16.8 — The Canonical Background Register

## Certified incoming baseline

- 3,552 tests
- 12,331 assertions
- all green

## Purpose

The Steward's Office now exposes the five optional Great Marketrealm Players Handbook backgrounds through a dedicated canonical register rather than requiring direct PHP edits.

The existing `HandbookBackgroundRegister` remains the immutable source baseline. Steward changes are stored separately in `gmrc_canonical_background_overrides` and can be restored to the Handbook baseline at any time.

## Steward-editable fields

- canonical display name;
- feature name;
- feature text;
- private Steward notes.

## Certified mechanical boundary

Skills and tools remain read-only in this slice. They already feed live Character mechanics through the Character Background value object, so changing them independently in administration would create disagreement between the Guild Library and certified Character records.

A follow-on background mechanics bridge may introduce validated future-character proficiency changes with explicit historical-snapshot rules.

## Source gaps

The Handbook transcription explicitly records when languages or equipment are not stated. The Steward Register surfaces those gaps rather than inventing missing canon.

## Next slices

- III.16.8A — Background Mechanics Bridge / future-character integration.
- Canonical Spell Register.
- Market Pass campaign invitation administration.
- Fellowship Invite Codes / Guild account linking.
