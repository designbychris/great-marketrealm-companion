# Phase III.11.2G — The Fellowship Hall Seal

Phase III.11.2 closes with a presentation, accessibility and regression
hardening pass across the complete Fellowship Hall.

## Fellowship Hall tabs

The opened Fellowship is now divided into four primary tabs:

1. Overview
2. Chronicle
3. Treasury
4. Company

### Overview

Contains the Company Portrait, Fellowship identity, Auby company note and
Company Charter.

### Chronicle

Contains the complete Company Chronicle, Adventure Note form and chronological
record.

### Treasury

Contains the shared company purse, deposit/withdraw controls, recent Treasury
Ledger and Quartermaster awareness.

### Company

Contains Company Offices, Fellowship Roster and Add Adventurer controls.

This keeps the Hall useful as its subsystems continue to grow without turning
the page into an increasingly long scroll.

## Accessible tab contract

The Hall follows the established accessible ledger-tab pattern:

- `role="tablist"`;
- `role="tab"`;
- `role="tabpanel"`;
- `aria-selected`;
- `aria-controls`;
- `aria-labelledby`;
- roving `tabindex`.

Keyboard support includes:

- Left Arrow;
- Right Arrow;
- Home;
- End.

Activating a tab can move keyboard focus when navigation was initiated from
the keyboard.

## Progressive enhancement

The Fellowship Hall remains fully readable without JavaScript.

Before the tab controller boots:

- the tab navigation is hidden;
- all Hall sections remain visible in their original document order.

Once JavaScript is ready, the tab navigation appears and inactive panels are
hidden.

This prevents the Hall becoming inaccessible if JavaScript is blocked or
fails to load.

## Remembered location

The Hall remembers the selected tab per Fellowship in local storage.

This is especially important for form workflows such as:

- adding an Adventure Note;
- depositing funds;
- withdrawing funds;
- changing Company Offices;
- adding/removing adventurers.

After a server redirect, the player returns to the Hall section they were
working in rather than being forced back to Overview.

An optional `gmrc_fellowship_tab` query parameter may override the remembered
tab for future deep-linking.

## Responsive and accessibility polish

The tab strip:

- scrolls horizontally rather than crushing labels on small screens;
- has clear focus-visible treatment;
- supports forced-colours/high-contrast mode;
- disables panel reveal motion under `prefers-reduced-motion`.

## Fellowship Hall seal

The hardening regression confirms that tabbing does not remove or weaken the
existing Fellowship systems:

- Company Portrait;
- Company Charter;
- Company Chronicle;
- Fellowship Treasury;
- Company Offices;
- Fellowship Roster;
- membership controls;
- Chronicle nonce;
- Treasury nonce;
- membership nonce.

No Party domain or persistence changes are required for III.11.2G.

## Phase III.11.2 status

With III.11.2G, **The Fellowship Hall** is considered sealed.

Future Fellowship work can build on the Hall as a stable presentation and
application surface rather than continuing to lengthen a single-page layout.

## Captured follow-up work

The subsequent roadmap still includes:

- Character Ledger links to the Fellowships a Character belongs to;
- personal Character currency tracking;
- safe transactional transfers between Character currency and Fellowship
  Treasury;
- the post-Fellowship class/progression completion pass for classes beyond
  the Wizard.
