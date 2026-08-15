# Phase III.10.4.1 — The Illuminated Register

The Adventurer's Register now displays each character's persisted Guild Illuminator portrait before the Ledger is opened.

## Portrait source

No new portrait storage or rendering system is introduced. `CharacterController::index()` already asks `PortraitRenderer::forCharacters()` for presentation-ready portrait view models. The Register now consumes the `PortraitViewModel` already passed to each `adventurer-entry`.

## Rendering

The card supports both persisted portrait modes:

- generated Guild Illuminator SVG;
- custom uploaded portrait image.

If neither representation is available, the existing character-initial fallback remains in place.

## Register interaction

The existing gold portrait frame is preserved. The portrait itself is now a keyboard-focusable link to the Character Ledger, with an accessible label naming the adventurer.

Generated SVG and custom images are constrained to the compact card frame rather than reusing the full Portrait Studio controls.

## Boundary

The Illuminated Register is presentation-only. It does not alter the portrait recipe, portrait persistence, Character state, Living Register, Vital Measures, or Guild Diceworks rules.
