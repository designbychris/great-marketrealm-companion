# Phase III.12.12B — Bard College Gifts

III.12.12B activates the seven Bard Colleges already certified by the College
Register and gives them real automatic Gifts of the Path.

## Canonical source

College mechanics are derived from **The Great Marketrealm - Players
Handbook**. This phase does not rename spells or add Character Creator
backgrounds; those handbook-wide catalogue updates remain reserved for the
post-Calling content pass.

## Shared gift architecture

Bard Colleges use the existing `PathGiftCatalogue` and `PathGifts` pipeline.
No Bard-only gift store or duplicate ledger is introduced.

`BardCollegeGiftProgression` registers all seven College identities and their
handbook features. Multiple gifts can share Level 3 where the handbook grants
two opening College features.

## Certified College gifts

- College of the Seasoned Song: Spice Notes, Herbal Harmonization, Choral
  Infusion, Symphony of the Senses.
- College of Nostalgia: Jingle Strike, Viral Catchphrase, Forgotten Favorite.
- College of Preservation: Canning Chant, Preserved Performance, Pickled Panic,
  Timeless Encore.
- Charcutaire: Cured Insight, Meatplatter Performance, Flavour Pairing, Cold
  Cut Wave.
- College of Culinary Crescendo: Sizzling Solo, Cook’s Toolkit, Boiling Over,
  Kitchen Orchestra.
- College of Confection: Sugar Sonata, Lickable Lullaby, Candy Clone.
- College of Churned Verse: Creamtone Cantrips, Harmonic Churn, Chill Out,
  Flavourful Refrain.

## Source-faithful cadence

Most Colleges provide gifts at Levels 3 / 3 / 6 / 14. College of Confection
provides one gift at 3 / 6 / 14. The supplied College of Nostalgia material
contains two Level 3 gifts and one Level 6 gift, but no Level 14 feature.
III.12.12B deliberately preserves that source rather than inventing a capstone.

## Register and choice previews

Because Bard Colleges now live in the shared gift catalogue, the existing
College Register automatically reports certified College Gifts and the shared
Character Creator candidate catalogue can show their first four future gifts.

## Regression boundary

The III.12.12B regression contract protects:

- all seven College registrations;
- canonical feature names and level order;
- non-empty player-facing summaries and details;
- unique gift keys;
- multiple opening Level 3 gifts;
- no premature Level 6 or Level 14 unlocks;
- the absence of an invented College of Nostalgia capstone;
- populated Bard College choice previews;
- the College Register transition from pending to certified gifts.
