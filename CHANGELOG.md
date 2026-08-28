
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
