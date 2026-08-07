# Auby Desk Phase II.1.1 Correction

Phase II.1 was authored against formatted development CSS/JavaScript while the
repository contains minified runtime files.

The intended replacements therefore did not match those runtime files.

Phase II.1.1 fixes this by applying explicit authoritative CSS overrides and
patching the exact minified JavaScript strings currently used by the plugin.

## Sleep contract

The `Zzz` layer now requires both:

- `data-guild-hall-daypart="late-night"`
- `data-ambient` containing `sleep`

All ambient layers are `display: none` until explicitly enabled.

## Hero composition

The wide afternoon scene now uses the intended zoomed-out composition and the
fade into Guild brown begins later, preserving more of Auby's workspace.
