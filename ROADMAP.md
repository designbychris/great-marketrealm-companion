- [x] IV.27E Companion — Shelfshine and server-readable magical illumination metadata.
- IV.27A — The Adventurer's Sight: Tabletop character projections now carry Companion-certified darkvision from the canonical race registry, including the Rindrunner Cave Hunter extension.

### Phase IV.26B — Weapons to Hand
The Companion now exposes an owner-scoped equipped-weapon projection for the Adventurer's Satchel. Attack math remains canonical to the Companion Character Ledger.
# Great Marketrealm Companion Roadmap

## Version 0.2

- Plugin architecture
- Character dashboard
- Character creation
- Character editing
- Admin improvements

---

## Version 0.3

- Character sheet
- Ability scores
- Saving
- Printing

---

## Version 0.4

- Inventory
- Equipment
- Weapons

---

## Version 0.5

- Magic Items
- Currency
- Encumbrance

---

## Version 0.6

- Race Manager
- Class Manager
- Background Manager

---

## Version 0.7

- Character Portraits
- Uploads
- Media integration

---

## Version 0.8

- Campaigns
- Parties
- Quest Log

---

## Version 0.9

- Spell Management
- Dice Roller
- Notes

---

## Version 1.0

First Public Release


---

## Tabletop Bridge

### Phase IV.25 — The Companion Character Gate
- Expose eligible owned Companion Characters to a seated Great Marketrealm Tabletop member through an explicit integration boundary.
- Validate Character ownership server-side before binding a Character reference to a Table seat.
- Project only deliberate Character identity fields first; combat state remains a later bridge concern.

### Phase IV.25A — The Adventurer’s Token Forge
- [x] Keep the Tabletop token separate from the Character portrait.
- [x] Fall back to the existing portrait until a dedicated token is supplied.
- [x] Allow a dedicated JPG, PNG or WebP token upload.
- [x] Persist non-destructive focus X/Y, zoom and token-ring choice.
- [x] Preview the token in the Character Ledger.
- [x] Expose a stable token projection for the future Tabletop adapter.
- [ ] Let the Tabletop consume the selected Character token after Phase IV.25 seat binding.
- [ ] Add optional pixel-sprite/token variants during the Pixel Chamber phase.

### Phase IV.25A.1 — The Token Forge Folio

- Surface the Adventurer’s Token Forge as a first-class **Tabletop Token** folio in the Character Ledger.
- Project the Companion Guild Profile portrait into the Tabletop member-avatar integration seam when available.
- Preserve WordPress avatar fallback and keep the full Character portrait independent from the Tabletop token recipe.


### Phase IV.25 — The Companion Character Gate
Companion now exposes owner-validated Character identity and forged token projections for the Great Marketrealm Tabletop.


## 0.3.1-alpha.4 — Phase IV.25.2: The Keeper Keeps Pace

- Makes Companion portrait projection explicitly owner-aware for trusted Tabletop consumers.
- Prevents a DM viewing another member's character from falling back to a generated portrait when that character has a custom Companion portrait.
- Preserves the existing current-user boundary for ordinary Companion portrait editing and persistence.


### Phase IV.26 — The Adventurer's Satchel (0.3.1-alpha.5)
- Companion support for the owner-scoped tabletop play projection and pull-out Adventurer's Satchel.
- Companion remains authoritative for character mechanics; Tabletop consumes the projection without duplicating character persistence.

- [x] IV.26C — The Spell Pouch: unfurled Satchel and Companion-authoritative spell projection.

- [x] IV.26D.1 — One Measure of the Adventurer: owner-scoped Tabletop writes reuse canonical Companion Adventuring Measures.

### Phase IV.34.2 — The Table Remembers Tonight
- [x] Companion owns the stable Campaign ↔ Tabletop ID relationship.
- [x] Active DM Campaigns and their linked Fellowship identity are exposed through an explicit Tabletop integration seam.
- [x] Existing and future Tabletop Sessions synchronise into the canonical DM Session Ledger.
- [x] Actual start/end timestamps and duration are preserved as Tabletop-certified Ledger metadata.
- [x] Existing DM prep notes, recap and attendance survive Tabletop synchronisation.
- [ ] IV.34.3 publishes the safe shared Session projection to the Fellowship Company Chronicle.

### Phase IV.34.3 — The Fellowship Remembers
- [x] Publish ended linked Tabletop Sessions into the Fellowship Company Chronicle as certified Company Deeds.
- [x] Keep active Sessions in the private DM Session Ledger until play concludes.
- [x] Persist immutable Tabletop Session provenance so backfill/re-sync updates rather than duplicates.
- [x] Keep DM prep notes and private recap text outside the shared Chronicle boundary.

### IV.34.6A — The Chronicle Opens Its Pages ✅
Promote linked Tabletop Sessions into durable Fellowship history: recap sync, character-attributed deeds, openable Session pages, Session-bound player notes, compact Chronicle previews, and friendly human dates.


### IV.34.6A.1 — The Archivist Straightens the Pages ✅
- Route remaining human-facing Session dates through the shared MarketRealm date presenter while preserving ISO storage/form values.
- Polish the Fellowship Session player-memory composer with the established Companion field/button language and accessible focus states.
- Correct the Chronicle privacy regression so it protects the short public preview while allowing recap/contribution provenance on the dedicated Session page.

- [x] IV.34.6A.3 — The Chronicle Knows Who Spoke: attributed/nested player memories, DM visibility, private Keeper-note boundary, and zero-minute masthead polish.
