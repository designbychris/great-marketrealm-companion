# Phase III.12.2B — The Fighter's Martial Paths

III.12.2B gives all six registered Fighter Martial Paths a real progression
through GMRC's existing Gifts of the Path system.

## Existing Fighter Paths

The bundled Character catalogue already registered:

1. Discontinued Lineage
2. Butcher
3. The Carver
4. Cutlery Knight
5. The Vineblade
6. Shelf Sentinel

III.12.2B does not replace or duplicate those Path identities.

## Shared gift cadence

Every Fighter Martial Path now progresses at the standard Fighter specialist
milestones:

- Level 3
- Level 7
- Level 10
- Level 15
- Level 18

Each Path has one automatic gift at each milestone.

The Level 3 gift can be discovered and certified during the same advancement
that records the Fighter's Martial Path.

Later gifts are granted by the same `PathGiftFolio` and
`GuildCertificationService` already used by Wizard Path Gifts.

## Discontinued Lineage

- 3 — Legacy Stock
- 7 — Out of Circulation
- 10 — Collector Grade
- 15 — Recall Resistant
- 18 — Never Truly Gone

This Path centres on obsolete, surprising and stubbornly persistent martial
techniques.

## Butcher

- 3 — Cleaver's Eye
- 7 — Joint Separator
- 10 — Prime Cut
- 15 — Cold-Room Discipline
- 18 — Master Butcher

This Path centres on anatomical precision, disciplined cuts and relentless
efficiency.

## The Carver

- 3 — Carver's Flourish
- 7 — Engraved Guard
- 10 — Signature Cut
- 15 — Living Masterpiece
- 18 — Gallery of Blades

This Path treats martial technique as deliberate craft and controlled artistry.

## Cutlery Knight

- 3 — Table-Ready Stance
- 7 — Silver Service
- 10 — Full Place Setting
- 15 — Banquet Guard
- 18 — Grand Service

This Path turns cutlery and formal service imagery into disciplined martial
protection.

## The Vineblade

- 3 — Tendril Footwork
- 7 — Grasping Cut
- 10 — Thorned Riposte
- 15 — Overgrown Battlefield
- 18 — Ancient Vine

This Path focuses on flowing movement, battlefield reach and plant-like
resilience.

## Shelf Sentinel

- 3 — Aisle Watch
- 7 — Stockroom Intercept
- 10 — Hold the Aisle
- 15 — Sentinel's Warning
- 18 — Unbroken Shelf

This Path is the dedicated defensive lane-holder and Fellowship protector.

## Persistence

No new persistence format is introduced.

Certified gifts continue to use the existing:

`_gmrc_path_gifts`

Character metadata and `PathGifts` value object.

This means Fighter and Wizard subclass progression now share one persistence
and certification lifecycle.

## Martial Register

III.12.2A's Fighter Martial Register now also lists **certified** Martial Path
Gifts.

The Register does not show future gifts as though the Fighter already owns
them.

Once a gift is granted through Guild Certification it appears in the Register
with its level, name and summary.

## Advancement flow

For the prepared Fighter moving from Level 2 to Level 3:

1. Specialist Folios identify Martial Path and Martial Path Gifts.
2. The player selects one of the six Martial Paths.
3. The Path Gift Folio discovers that Path's Level 3 gift.
4. Both the Path and its first gift are ready for one Guild certification.
5. Certification persists the Path and gift.
6. The Martial Register displays the chosen Path and certified Level 3 gift.

At Level 7 the same shared machinery exposes the second gift without asking
for the Path again.

## Design boundary

The gifts introduced here establish identity, progression, summaries and
persistence.

They are not yet a complete combat-rules engine for every subclass feature.

Where a gift later needs a roll button, limited-use resource, reaction,
condition or target integration, that should be connected through the same
shared active-play systems rather than embedding bespoke JavaScript into each
Path.
