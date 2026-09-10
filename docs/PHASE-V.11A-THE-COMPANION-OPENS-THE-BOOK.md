# Phase V.11A — The Companion Opens the Book

Phase V.11A teaches the Great Marketrealm Companion to consume **active** Great Marketrealm Almanac character content through the read-only GMREXP Active Content API introduced in Expansions V.11.

## Boundary

GMREXP remains the canonical owner of expansion definitions. The Companion does not import or clone Almanac mechanics into a second catalogue. Instead it projects active sourcebook definitions into existing character-creation seams and stores only the minimum identity snapshots needed to keep an already-created Character readable if an Almanac is later deactivated.

**One canonical definition, multiple consumers.**

If Great Marketrealm Expansions is absent, disabled, incompatible, or temporarily unable to answer, the Companion gracefully falls back to its native and Steward-owned content.

## Character-creation projections

The Companion can now augment its existing character catalogue with active Almanac:

- races;
- backgrounds; and
- subclasses whose parent Calling already exists in the Companion.

Native Companion identities always win a local-key collision. If two active Almanacs expose the same local identity, the expansion adapter refuses to guess between them. Every accepted expansion projection retains its fully-qualified GMREXP canonical ID and Almanac key.

Base expansion Classes are deliberately not introduced by this phase. A new Calling needs a complete progression/mechanics contract rather than merely a label in a select field.

## Existing Characters survive later deactivation

Creation persists the fully-qualified expansion IDs for race, background and subclass provenance. Race/background display and registration snapshots retain enough inscription-time identity to keep an existing Character readable after the source Almanac is later switched off.

That does **not** turn the Character into a second canonical sourcebook. Expansion mechanics remain owned by GMREXP.

## Mechanics projection

Where an Almanac provides structured fields already understood by Companion, Phase V.11A projects them through existing domain services. This includes supported background skills/tools/languages and supported structured race grants such as proficiencies, languages, resistances and explicit ability-score rules.

Subclass feature names/descriptions and progression are exposed as read-only choice/preview information. They are **not** silently registered as native Companion Path Gifts. A later bridge phase will connect sourcebook subclass progression to the advancement engine through an explicit, tested mechanics contract.

## The next bridge: the DM shares the book

Phase V.11A intentionally consumes today's active-content boundary without hard-coding a commercial model. The longer-term activation model is:

**Available → Entitled → Campaign Active → Consumable**

The intended table flow is:

1. A DM is entitled to an Almanac (free entitlement can simply return yes).
2. The DM activates the Almanac for a Campaign/Fellowship.
3. Linked players may use that Almanac's character options for that table.
4. Companion and Tabletop consume the same canonical GMREXP IDs.

Entitlement, payment/donation, campaign activation and character provenance remain separate concerns. That lets the MarketRealm keep expansions free today without closing the door on optional supporter or paid models later.

## Non-goals

Phase V.11A does not:

- duplicate an Almanac into Companion storage;
- make inactive Almanac content selectable for new Characters;
- create new base Calling progression from an expansion label alone;
- apply expansion subclass progression as native Path Gifts;
- implement DM/Fellowship inherited activation yet; or
- modify Great Marketrealm Tabletop.
