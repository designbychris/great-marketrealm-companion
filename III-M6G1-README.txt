Phase III.M.6G.1 — The Adventurer Rolls Their Training

Apply over the ALL GREEN III.M.6G baseline (3986 tests / 14983 assertions).

Changes:
- Native ability checks, saving throws and skills can roll through shared Guild Diceworks.
- Canonical ability modifiers added to Pocket character payload; native does not calculate them.
- Normal / Advantage / Disadvantage.
- Secure d20 RNG via crypto.getRandomValues with rejection sampling.
- Six-entry in-memory recent roll ledger; no native character persistence.
- Natural 20 celebration and Natural 1 lonely-confetti/Auby reaction.
- Diceworks stays mounted across dashboard tabs and starts collapsed.
- Adds the requested breathing room between Adventuring Measures and AC/Initiative/Speed/Proficiency.

Run:
php vendor/bin/phpunit --display-warnings

Then on Windows:
cd native\pocket-companion
npm run build
npm run android:prepare

Android Studio -> Play -> Pixel.
