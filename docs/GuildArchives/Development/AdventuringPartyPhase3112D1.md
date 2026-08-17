# Phase III.11.2D.1 — Company Offices Regression Alignment

The first III.11.2D PHPUnit run exposed one formatting-sensitive regression.

## Cause

The Fellowship Hall correctly renders Company Office glyphs and labels using
a multiline fluent call:

- `->office()`
- `->glyph()`
- `->label()`

The regression incorrectly expected `office()->glyph()` and
`office()->label()` to appear as contiguous one-line source strings.

## Correction

The regression now verifies the actual method-chain elements independently.

No Company Office domain, persistence, application, route, nonce, controller,
or Fellowship Hall runtime behaviour changes in this correction.
