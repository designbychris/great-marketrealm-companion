# Changelog

## 0.3.1-alpha.11.2 — Phase V.11A.2: The DM Shares the Book

- Added Campaign-scoped Almanac sharing in the Dungeon Master Command Centre.
- Added persistent Campaign expansion-key storage without copying canonical expansion definitions into Companion.
- Added player inheritance of shared Almanacs through active Campaign roster membership.
- Kept the entitlement seam replaceable: current site-active GMREXP Almanacs use a free-entitlement policy while future payment/donation providers can be introduced independently.
- Scoped Companion expansion races, backgrounds and subclasses through one `gmrc_expansion_character_scope` filter so UI, validation and provenance use the same access decision.
- Preserved temporarily unavailable Campaign selections while preventing inactive Almanacs from being consumed.
- Added expansion presentation metadata and accessible sourcebook badges to Character Generator Race, Background and Subclass cards.
- Added the first sourcebook-specific visual treatment for The Midnight Menu with a neon-inspired edge/glow plus reduced-motion and forced-colour fallbacks.
- Added regression coverage for Campaign sharing, player inheritance, canonical key storage, scope filtering, UI provenance and future entitlement separation.

## 0.3.1-alpha.11 — Phase V.11A: The Companion Opens the Book

## 0.3.1-alpha.11.1 — Phase V.11A.1: Pippin Discovers That Bridges Need Expansion Joints

- Fixed a WordPress boot fatal when the Companion container attempted to auto-wire the optional `Closure` seam on `ExpansionCharacterCatalogue`.
- Registered the production expansion adapter explicitly so Companion/Expansions plugin load order cannot make the container instantiate `Closure`.
- Updated the Background Workshop regression contract to reflect V.11A shared resolved backgrounds rather than Steward-prefix-only rendering.
- Added a regression assertion protecting the explicit expansion-adapter container binding.


- Adds a read-only Companion adapter for GMREXP Active Content API 1.0.0.
- Active Almanac races, backgrounds and subclasses augment existing Character creation choices without duplicating canonical sourcebook definitions.
- Preserves fully-qualified expansion provenance for newly created Characters and keeps existing expansion race/background identities readable after later Almanac deactivation.
- Projects only structured mechanics already understood by Companion; expansion subclass progression remains a read-only preview until a dedicated advancement bridge.
- Fails safely back to native/Steward content when GMREXP is absent or incompatible.
- Records the future entitlement/campaign model as **Available → Entitled → Campaign Active → Consumable**, keeping payment policy separate from content architecture.

## IV.34.2 — The Table Remembers Tonight

- Adds the Companion-owned persistent Campaign ↔ Tabletop link.
- Exposes owner-scoped active Campaign choices and linked Fellowship identity to the Tabletop through explicit WordPress filter contracts.
- Upserts Tabletop play Sessions into the existing DM Session Ledger by immutable Tabletop Session ID, with safe same-number adoption for an existing unlinked Session.
- Records actual start/end timestamps and calculated duration while preserving DM prep notes, recap and attendance.
- Adds an `In Progress` Session state and presents the linked Tabletop record in the Campaign Command Centre and Session Ledger.
- Reserves Company Chronicle publication for IV.34.3 so private DM Ledger data remains private.

## IV.27E — Light Wrought by Magic

- Canonised **Shelfshine** as the Great Marketrealm rename of Light and added structured 20 ft bright + 20 ft dim, one-hour magical illumination metadata for Tabletop.

- IV.27A — The Adventurer's Sight: Tabletop character projections now carry Companion-certified darkvision from the canonical race registry, including the Rindrunner Cave Hunter extension.

## 0.3.1-alpha.8 — IV.26D.1 One Measure of the Adventurer
- Exposes the Companion's existing mutable Adventuring Measures through an owner-scoped Tabletop filter boundary.
- Reuses `Character::updateVitalMeasures()` and `CharacterRepository::save()`; Maximum HP remains Companion-certified and read-only.
- Returns a fresh authoritative character projection after each successful Tabletop update.


## 0.3.1-alpha.6 — Phase IV.26B: Weapons to Hand
- Projects the character owner's equipped Companion attacks into the Tabletop play snapshot.
- Adds owner-aware inventory lookup so a Tabletop projection cannot accidentally inspect the current viewer's inventory.
- Reuses the canonical AttackPresenter for attack bonus, damage, range and weapon properties.
# Changelog

All notable changes to this project will be documented here.

The format is based on Keep a Changelog.

---

## [0.3.1-alpha.2] — Phase IV.25A.1: The Token Forge Folio

- Moves the Adventurer’s Token Forge into its own visible **Tabletop Token** Character Ledger tab/folio.
- Keeps the portrait and Tabletop token as separate visual identities while retaining portrait fallback.
- Bridges Companion Guild Profile portraits into the Tabletop roster through `gmrt_table_member_avatar_url`, with the existing WordPress avatar retained as fallback.
- Adds regression coverage for the dedicated Ledger panel and cross-plugin avatar seam.

## [0.3.1-alpha.1] — Phase IV.25A: The Adventurer’s Token Forge

### Added
- Dedicated per-Character Tabletop token recipe, deliberately separate from the full Companion portrait.
- Safe portrait fallback when no dedicated token has been forged.
- JPG, PNG and WebP token uploads capped at 4 MB.
- Non-destructive token focus, zoom and ring/frame controls with a live Ledger preview.
- Stable token presenter intended for the forthcoming Great Marketrealm Tabletop character bridge.
- Owner-bound persistence and a dedicated nonce route for token mutation.

### Quality
- Added Token Forge regression coverage for persistence separation, upload validation, crop recipe, routes, nonce boundary and Ledger presentation.

---

## v0.6.0 — Framework Foundation

### Core
- Dependency Injection Container
- HTTP Request
- HTTP Response
- Application
- Kernel
- Router

### Quality
- 113 PHPUnit tests
- 130 assertions
- 100% passing

The framework foundation is now considered stable and ready
for higher-level services and application modules.

---

## [0.2.0] - Unreleased

### Added

- New plugin architecture
- Autoloader
- Character Manager
- Dashboard
- Character database
- Admin framework

---

## [0.2.0-alpha3.2] - Unreleased

### Added

- Introduced the new dependency injection Container.
- Added Application as the central platform object.
- Registered core framework services.
- Registered Application, Container and Kernel in the service container.

### Changed

- Simplified platform bootstrapping.
- Improved framework architecture ready for service providers.

 ---

## 0.2.0-alpha2

Added

- DatabaseManager
- CharacterRepository
- Character model
- Seeder

Changed

- Admin architecture

Fixed

- Dashboard rendering

---

## [0.2.0-alpha1]

### Added

- PSR-4 style autoloader
- Core plugin bootstrap
- Admin framework
- Shortcode framework
- Asset management
- Namespaced architecture

---

## [0.1.0] - Released

### Added

- Initial plugin
- Installer
- Database creation
- Dashboard shortcode
- Admin menu

## Phase IV.25 — The Companion Character Gate
- Exposes owner-scoped Companion character projections to the Tabletop through filter-based integration seams.
- Carries the forged Tabletop Token recipe across the boundary without replacing the Character portrait.
- Adds owner-aware token recipe lookup for trusted cross-account Table presentation.


## 0.3.1-alpha.4 — Phase IV.25.2: The Keeper Keeps Pace

- Makes Companion portrait projection explicitly owner-aware for trusted Tabletop consumers.
- Prevents a DM viewing another member's character from falling back to a generated portrait when that character has a custom Companion portrait.
- Preserves the existing current-user boundary for ordinary Companion portrait editing and persistence.


### Phase IV.26 — The Adventurer's Satchel (0.3.1-alpha.5)
- Companion support for the owner-scoped tabletop play projection and pull-out Adventurer's Satchel.
- Companion remains authoritative for character mechanics; Tabletop consumes the projection without duplicating character persistence.

## 0.3.1-alpha.7 — IV.26C The Spell Pouch
- Projects owner-scoped learned cantrips and spells to the Tabletop from the Companion Arcane Pantry.
- Exposes casting ability, spell attack, save DC and slot maxima without duplicating spell mechanics in the VTT.

## Phase IV.34.3 — The Fellowship Remembers
- Ended linked Tabletop Sessions now write a certified Company Deed into the Fellowship Company Chronicle.
- Chronicle Session records are idempotent by immutable Tabletop Session ID and preserve their original Chronicle identity on re-sync.
- The shared projection contains only safe play facts; DM prep notes and private recap text remain in the Dungeon Master's Session Ledger.

### Phase IV.34.6A — The Chronicle Opens Its Pages
- Tabletop recaps now populate the linked Dungeon Master Session Ledger record.
- Company Chronicle Session deeds now open a full shared Session page in a new tab.
- Full Fellowship Session pages show the recap, character-attributed deeds, and Session-bound player notes.
- Added reusable MarketRealm friendly date presentation (`3rd September 2026`) and applied it to Session/Chronicle surfaces.


### Phase IV.34.6A.1 — The Archivist Straightens the Pages
- Finished the friendly-date sweep on Dungeon Master Session cards and Campaign Session status surfaces.
- Styled the dedicated Fellowship Session player-memory composer to match the existing Companion UI.
- Updated the Chronicle privacy regression to distinguish the bounded public Company Deed preview from safe recap/contribution source metadata used by the full Session page.

### Phase IV.34.6A.3 — The Chronicle Knows Who Spoke
- Attribute Session memories to Guild account and adventurer snapshots.
- Nest player memories under their certified Session in the Company Chronicle with a native Show/Hide disclosure.
- Project shared player memories into the DM Session Ledger while keeping DM preparation private.
- Hide meaningless sub-minute `0m` duration labels.
- Remove the nonce regression test interpolation warnings.

- IV.35.10B — The Boss Would Like to Participate: complete the Boss Lair Bestiary bridge, qualified-defense compatibility, and live-created Keeper token interaction path.
