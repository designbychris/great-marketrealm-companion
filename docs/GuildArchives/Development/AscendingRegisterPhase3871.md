# Character Lifecycle Initiative — Phase III.8.7.1
## Choice Guidance & Readiness

Phase III.8.7.1 improves the player experience for `choose-n` advancement
folios without weakening the server-side rules.

## Guidance

Multi-choice folios now explain what the adventurer is being asked to do.

Spellbook example:

> Looks like you can learn some new spells! Select 2 spells to add to your
> spellbook.

Cantrip and generic choose-N folios use the same framework with appropriate
nouns.

## Live readiness

The form exposes:

- choice mode
- minimum selections
- maximum selections
- choice kind

`advancement-choice-readiness.js` counts the currently checked values and
updates an accessible `aria-live="polite"` status.

Examples:

- `0 of 2 spells selected — choose 2 more.`
- `1 of 2 spells selected — choose 1 more.`
- `2 of 2 spells selected — ready to record.`

## Submit state

For choose-N folios, **Record Choice** begins disabled unless the saved state
already contains a valid number of selections.

The button is enabled only when:

`selected >= minimum && selected <= maximum`

If more than the maximum is selected, the status asks the player to remove the
excess selection(s).

## Security / validation boundary

The browser readiness controller is UX only.

The existing server-side Choice Folio validation remains authoritative. A
crafted request cannot bypass the minimum or maximum selection rules merely by
removing the `disabled` attribute.

## Reuse

This controller is intentionally not Wizard-specific. It can support future:

- Wizard spell choices
- cantrip choices
- Warlock spells or invocations
- path/subclass selections
- talents
- any future choose-N advancement folio
