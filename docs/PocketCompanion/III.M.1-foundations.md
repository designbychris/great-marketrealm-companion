# III.M.1 — Pocket Companion Foundations (initial implementation)

## Scope
First release targets iOS and Android phones. Tablet-specific layout is deferred, though iPads are available for compatibility testing. Initial features: existing-account sign-in, character listing/sheet, HP and Guild Diceworks. No new user or character database.

## Repository audit
- WordPress plugin bootstrap: `great-marketrealm-companion.php`; app/container and module routes live in `app/Core`, `app/Providers/RouteServiceProvider.php`, `app/Modules/Characters/Routes.php`.
- Existing `/characters` and `/characters/{id}/vital-measures` routes are **Companion page/form routes**, not automatically a native-client JSON contract. Do not reuse their POST form route from an app without reviewing nonce/session/owner enforcement.
- `CharacterRepository::all()` delegates to `allForOwner(get_current_user_id())`; `find()` delegates to `findForOwner(...)`. The initial mobile endpoint uses the existing owner-scoped `all()` method, not a cross-account query.
- `Character::hitPoints()` exposes current, maximum and temporary HP. The existing web controller validates current HP within 0..maximum and temporary HP within 0..999 before saving.
- Guild Diceworks and the living portrait UI require a separate client-side portability and performance audit. Do not claim these work natively yet.
- No native Capacitor/iOS/Android project is included in this phase. PHP remains on the server; a future mobile client consumes authenticated HTTPS JSON.

## Implemented contract
`GET /wp-json/gmrc-pocket/v1/characters` returns `{ "characters": [{"id": "...", "name": "...", "hp": {"current": 1, "maximum": 1, "temporary": 0}}] }` for the authenticated user. Unauthenticated requests are denied by WordPress REST permission handling. This is deliberately **read-only**; it cannot change HP or expose other players' characters. Response requests private/no-store caching.

## Security and next gates
1. Choose and implement a supported mobile sign-in/token lifecycle; a WordPress browser cookie alone is not a finished native-app authentication design. Never store the player's WordPress password in the app or ship an admin/API secret.
2. Add authenticated integration tests (anonymous denied, owner sees only own characters, no cross-account access), then test on a staging WordPress installation over HTTPS. The new endpoint is not production-certified without these checks.
3. Add a dedicated owner-scoped HP write endpoint with schema validation, concurrency handling, permissions and integration tests. Do not expose generic `save()` by character ID.
4. Audit Diceworks randomness and choose whether rolls are local or authoritative/server-recorded; audit portrait assets and mobile memory use.
5. Build a phone-first client prototype, test on Pixel and iPhone, and check the recovered MacBook Air model/macOS against current Xcode requirements. Test iPads for basic compatibility even before dedicated tablet layouts.

## Deployment caution
This is a narrow, additive API proof of foundation, **not** an installable mobile app. Do not deploy directly to production before normal regression tests and staging authorization checks. Existing WordPress site/database remain canonical.
