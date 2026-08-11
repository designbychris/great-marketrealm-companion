# Private Studio — Persisted Portrait Lifecycle Fix

## Symptom

On Edit Adventurer the generated portrait briefly appeared correctly and then
lost its race/body and class/outfit layers, leaving universal face layers such
as the eyes visible. Workbench Body Form and Outfit controls subsequently
stopped changing the portrait.

## Cause

The original monolithic `portrait-studio.js` Character Creator controller
initialised every portrait that lived inside a form.

The Private Studio also lives inside the Edit Character form, but race and
class are read-only there rather than checked creation radios. The legacy
controller therefore resolved both values to empty strings and rebuilt the
portrait using a blank provisional recipe.

This also erased the studio's race/class data attributes, preventing the
modular Workbench from finding valid race/class variants.

`generation2.js` had the same lifecycle assumption and could also replace a
persisted Generation 2 collection.

## Contract

- Character Creation may build provisional portrait recipes.
- Persisted Character/Edit portraits are rendered by PHP.
- The Private Studio may replace individual persisted layers through the
  modular Workbench.
- Provisional creator engines must never rebuild a persisted portrait.
