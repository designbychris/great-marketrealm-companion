# Phase III.11.1E.1 — Fellowship Register Regression Alignment

The first III.11.1E test run exposed two stale structural assertions.

## Member form location

III.11.1D kept role/update/remove forms directly inside `parties/show.php`.

III.11.1E correctly extracted those controls into the reusable
`components/entries/fellowship-member.php` component.

The HTTP regression now verifies the membership nonce and PUT/DELETE method
contracts in that reusable component while continuing to verify the Add
Adventurer form in the Fellowship view.

## Guild furniture rendering

The Fellowship Register uses the established direct furniture include pattern:

- `components/furniture/guild-page.php`
- `components/furniture/guild-ledger.php`

The presentation regression now verifies those concrete include paths instead
of expecting dotted component identifiers that are not used by this view.

No Fellowship runtime, persistence, portrait, route or application behaviour
changes in this correction.
