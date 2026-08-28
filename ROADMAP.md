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
