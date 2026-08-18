# Phase III.11.3C.2 — Character Ledger Shell Repair

Browser testing after Fellowship Bonds exposed a Character Ledger rendering
failure that initially looked like a missing closing HTML element.

## Root cause

The Character Ledger Fellowship card renders a member's Fellowship role using:

`$membership->role()->label()`

`PartyMembershipRole` exposed `value()` and `isLeader()`, but did not yet expose
the presentation method `label()`.

For Characters who belong to a Fellowship, PHP therefore threw while rendering
the Archive Notes portion of the Ledger.

Because the exception occurred before the full Character view had finished
rendering, the final Ledger markup was never reached. In the browser this
presented like a structural HTML failure:

- the Companion navigation appeared to be missing;
- the Character content began too close to the site header;
- the WordPress/theme footer appeared constrained to the Ledger width.

The source HTML itself was balanced; rendering was simply terminating before
the source could finish.

## Repair

`PartyMembershipRole` now provides:

- `Leader`
- `Member`

through a first-class `label()` method.

The Fellowship card can therefore finish rendering and the normal Companion
layout regains control before the WordPress theme footer renders.

## Removal of the temporary workaround

The earlier III.11.3C.1 CSS/layout-boundary workaround was based on the visual
symptoms rather than the runtime cause.

It has now been removed:

- `data-character-ledger-boundary` is removed from the Character Ledger;
- the added Ledger bottom-margin/isolation/clearfix CSS is removed.

This restores the original Character Ledger geometry rather than layering
extra spacing over a runtime error.

## Regression protection

Regression coverage now protects:

- the `PartyMembershipRole::label()` contract;
- the Character Fellowship card's use of that contract;
- removal of the temporary layout workaround;
- balanced Character Ledger source markup.

No purse, Fellowship membership, Treasury or Coin Between Companions domain
behaviour is changed by this repair.
