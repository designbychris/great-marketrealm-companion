# Phase III.12.13B — The Artificer Specialisation Gifts

## Guild Seal

**Status:** Implemented — awaiting server PHPUnit certification.

Phase III.12.13B brings the four registered Artificer Specialisations into
the shared Gifts of the Path catalogue using The Great Marketrealm - Players
Handbook as the canonical mechanics source.

## Certified Specialisations

### The Spice Engineer

Supplied cadence: **3 / 3 / 5 / 9 / 15**

- Level 3 — Spicecrafting
- Level 3 — Infused Condiments
- Level 5 — Flavour Cascade
- Level 9 — Gourmet Arsenal
- Level 15 — The Grand Seasoning

### The Cheesemonger

Supplied cadence: **3 / 3 / 5 / 9 / 15**

- Level 3 — Cheesy Constructs
- Level 3 — Cheese-Forged Infusions
- Level 5 — Dairy Density
- Level 9 — Cheese Overload
- Level 15 — Grand Gruyère

### The Sous-Sorcerer

Supplied cadence: **3 / 3**

The handbook does not provide a Level 5, Level 9 or Level 15 progression for
this Specialisation. Its unlevelled Core Features are certified at the
Artificer Specialisation selection boundary rather than inventing unsupported
later features.

- Level 3 — Sous-Sorcerer Core Features
- Level 3 — Flavour Surge

### The Culinary Engineer

Supplied cadence: **3 / 3 / 5 / 9 / 15**

- Level 3 — Tools of the Trade
- Level 3 — Culinary Infusions
- Level 5 — Battle Feast
- Level 9 — Animated Utensils
- Level 15 — Master of Magical Cuisine

## Shared Gift Architecture

`ArtificerSpecialisationGiftProgression` joins the existing
`PathGiftCatalogue`. The Companion therefore gains Artificer Specialisation
support without a separate gift persistence system.

The existing `PathCandidateCatalogue` automatically exposes Gift previews for
all four Artificer candidates.

The existing Artificer Specialisation Register automatically changes from
"Specialisation Gifts await their dedicated phase" to
"Specialisation Gifts certified" when a supported Specialisation is chosen.

## Source-Fidelity Hardening

The Register's future milestone projection now checks the actual supplied Gift
catalogue before promising Level 5, Level 9 or Level 15 Specialisation Gifts.

This matters for The Sous-Sorcerer: after its Level 3 package, the Register
continues to ordinary Artificer milestones such as Tool Expertise instead of
inventing unsupported Specialisation features.

## Regression Boundary

The III.12.13A tests that deliberately required Specialisation Gifts to remain
absent are advanced to the III.12.13B certified state.

Dedicated III.12.13B regressions protect:

- all four shared-catalogue registrations;
- canonical Gift names and level cadences;
- all 17 supplied Gift keys;
- player-facing summaries and details;
- candidate Gift previews;
- Level 3 simultaneous unlocks;
- later-level gating;
- the Culinary Engineer Level 15 capstone;
- the deliberately uneven Sous-Sorcerer progression;
- source-aware Register milestones.

## Next Slice

**Phase III.12.13C — Artificer Specialisation Gifts on the Living Ledger**

That phase may project currently available and next supplied Specialisation
Gifts into the active Character Ledger while leaving certified advancement
history untouched.
