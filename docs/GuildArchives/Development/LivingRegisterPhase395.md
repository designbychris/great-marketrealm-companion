# Phase III.9.5 — The Measure of the Journey

## Purpose

III.9.5 gives the Living Register a compact historical measure of the adventurer's certified growth.

The Sealed Chronicle remains the detailed record of each certification. The Measure of the Journey is only an aggregate view over those same immutable entries; it is not a new progression store and cannot mutate the Character.

## Recorded measures

The Living Register derives:

- completed Guild Certifications;
- total maximum hit points gained through certification;
- spells entered through advancement choices;
- cantrips entered through advancement choices;
- Path Gifts granted by sealed progression;
- Guild Milestone marks recognised by the Chronicle;
- the first and latest retained certification timestamps for future presentation use.

## Source-of-truth rule

Every figure is calculated from the Chronicle returned by `LivingRegisterPresenter`. If an event was not sealed into certification history, it does not appear in the journey measure.

This deliberately excludes mutable play-state such as current HP and temporary HP. Those belong to Adventuring Measures rather than certified progression history.

## Presentation

The Character Ledger renders **The Measure of the Journey** immediately before the Sealed Chronicle, providing a quick overview before the player reads the individual historical entries.

## Future boundary

III.9.5 remains descriptive. Achievements, honours, memorable dice rolls, and campaign journals are separate systems and should reference the Living Register rather than becoming part of its persistence model.
