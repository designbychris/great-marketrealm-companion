# Phase III.10.14 — Diceworks Accessibility & Motion Polish

The Guild Diceworks now receives a dedicated accessibility, reduced-motion and responsive-quality pass without changing its game rules.

## Accessible tray semantics

The Diceworks tray is an explicitly labelled interactive region with a screen-reader description explaining that visual dice and confetti are decorative and that roll results are announced in text.

The existing live region is now an explicit `role=status` region.

The result copy has a programmatic focus target so keyboard users can move directly to the newly produced result or its next available action.

## Keyboard-aware focus management

Diceworks distinguishes keyboard interaction from pointer interaction.

After a keyboard-triggered roll, focus moves to the most useful next location:

1. Critical Damage action when a natural-20 attack created one;
2. Vital Application action when a resolved roll can be applied;
3. otherwise the result summary.

Pointer users do not have focus unexpectedly stolen after a click.

Closing Diceworks continues to restore focus to the originating trigger.

## Screen-reader roll announcements

Structured result announcements add explicit non-visual meaning for:

- Natural 20;
- critical-hit availability;
- Natural 1;
- Auby's Natural-1 line;
- resolved targets;
- reference-only targets.

The Nat20/Nat1 humour therefore remains understandable when confetti is invisible or motion is disabled.

## Reduced motion

Diceworks now checks `prefers-reduced-motion` in JavaScript before starting the dice-roll animation, avoiding unnecessary forced-reflow animation work.

CSS also disables decorative motion and transitions throughout the tray while preserving textual reaction banners and Auby quotes.

## Large dice pools

Pools larger than twelve dice receive an `is-huge-pool` state with:

- responsive auto-fit columns;
- bounded vertical height;
- internal scrolling;
- compact but readable die values.

The maximum free-roll quantity remains 20.

## Mobile tray

On small screens the tray is bounded to the viewport height and scrolls internally. Result text, history entries, Quick Rolls and large pools are allowed to wrap rather than overflow.

## High contrast

A `forced-colors: active` pass protects borders, kept-die indication and focus outlines in operating-system high-contrast modes.

## Boundary

III.10.14 changes presentation, accessibility and interaction ergonomics only. Dice formulas, target resolution, vitality rules, history persistence and critical logic remain unchanged.
