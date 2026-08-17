# Phase III.11.2A — The Company Portrait

Phase III.11.2 — The Fellowship Hall begins by separating the compact
Fellowship Register preview from the large open-Fellowship company
illustration.

## Portrait presentation variants

The reusable Fellowship portrait now accepts an explicit presentation variant:

- `compact`
- `company`

The Fellowship Register uses `compact`.

The open Fellowship Hall uses `company`.

Unsupported values safely fall back to `compact`.

## Company canvas

The company variant receives a substantially taller responsive landscape
canvas so full adventurer silhouettes are not cropped after the individual
portrait backgrounds and frames are removed.

The desktop Fellowship Hall canvas scales from roughly 32rem to 43rem in
height and uses a 16:10 illustration proportion.

Tablet and mobile breakpoints preserve useful vertical space without changing
the compact Register cards.

## Ensemble composition

The company portrait has explicit composition contracts for one through six
visible adventurers.

The layouts evolve from:

- one centred hero;
- a balanced two-adventurer pair;
- a three-character leader-forward triangle;
- a widened four-to-six adventurer ensemble.

The composition retains the III.11.1E.2 rule that generated Character
backgrounds and individual portrait frames are removed from the company
illustration.

## Register independence

Compact Fellowship Register portraits retain their existing dimensions.

This prevents the Fellowship Hall hero polish from making the Party listing
oversized.

## Future direction

The `company` presentation context is now an explicit reusable contract.

Later Fellowship Hall phases can add:

- Fellowship banners;
- company backdrops;
- palette identity;
- portrait arrangement controls;
- exportable party artwork;

without coupling those features to the compact Register preview.
