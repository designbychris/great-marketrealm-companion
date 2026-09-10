# Phase V.11A.2 — The DM Shares the Book

Phase V.11A.2 gives the Companion a Campaign-scoped sharing layer for active Great MarketRealm Expansion Almanacs.

## The access model

The long-term boundary is deliberately explicit:

**Available → Entitled → Campaign Active → Consumable**

For the current free entitlement policy, every GMREXP Almanac that is active at site level is treated as available to a Dungeon Master. The Dungeon Master may then choose which of those Almanacs are shared with each active Companion Campaign. Players inherit the union of the Almanacs shared by active Campaigns whose rosters contain them.

This is intentionally not a payment implementation. A future payment, donation, supporter, licence, or other entitlement provider can replace the current free entitlement policy without changing Campaign sharing or the Character Generator consumer contract.

## Ownership boundary

**GMREXP remains canonical.**

The Companion stores only Almanac keys on a Campaign. It does not copy races, backgrounds, subclasses, features, monsters, items, or other sourcebook definitions into Companion storage. Shared therefore does not mean copied.

If an Almanac that was previously shared is temporarily made inactive in GMREXP, its Campaign key remains configured but its content is not consumable. Reactivating the Almanac makes that existing Campaign choice effective again without reconstructing sourcebook data.

Archived Campaigns do not grant expansion access and their Almanac selection is read-only.

## Dungeon Master workflow

The Campaign Command Centre contains a **Campaign Almanacs** card. It shows character-facing Almanacs currently available from GMREXP and allows the Dungeon Master to share selected books with that Campaign.

A Dungeon Master can continue to see the currently available expansion content in their own Companion workflow. A normal player sees expansion character options only when they are rostered into an active Campaign that shares the corresponding Almanac.

Existing Campaign/Fellowship roster relationships remain the membership boundary. If a Fellowship supplies or synchronises a Campaign roster, those linked players receive the same inherited access through that roster rather than through a second expansion-membership system.

## Character Generator provenance

Expansion-derived Race, Background, and Subclass choices keep their canonical GMREXP identity and now expose presentation metadata to the Character Generator.

Native Companion choices keep the normal Companion treatment. Expansion choices receive a sourcebook badge as a textual cue plus expansion-aware styling hooks. The first presentation treatment is for **The Midnight Menu**, which receives a neon-inspired edge/glow treatment while still displaying its sourcebook name so provenance is not communicated by colour alone.

The hooks are generic rather than hard-coded into the generator's mechanics, allowing future books such as Withered Reach to use a different visual language without changing selection logic.

Reduced-motion and forced-colour fallbacks are retained so decoration never becomes required information.

## Boundaries retained

- Available is not the same as Campaign Active.
- Entitled is not the same as Campaign Active.
- Shared is not copied.
- GMREXP activation still determines whether canonical content is presently available.
- Companion Campaign sharing determines which linked players may consume that available content.
- Character snapshots remain readable if an Almanac is later unshared or deactivated.
- Tabletop is not modified by this phase. The corresponding VTT consumer work belongs to V.11B and can use the same Campaign-active Almanac identities rather than inventing another activation system.
