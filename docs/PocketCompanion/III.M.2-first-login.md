# III.M.2 — First Login: authenticated API contract (incremental)

## Delivered in this patch
- Adds `GET /wp-json/gmrc-pocket/v1/session`, authenticated with the existing WordPress REST authentication mechanism. It returns only `authenticated`, user ID and display name; it never returns passwords, tokens or email addresses.
- Preserves the III.M.1 owner-scoped, read-only characters endpoint and the existing web login.
- This endpoint is a session check, **not a native login endpoint**. It can be exercised in an authenticated browser using a valid WordPress REST nonce or through a separately configured, supported REST authentication method.

## Security boundary and next implementation gate
A native iOS/Android sign-in handoff is **not yet implemented**. Do not send username/password to a custom REST route, embed application passwords or a shared secret in the mobile bundle, or assume that an external browser's WordPress cookies automatically authenticate a Capacitor WebView. Select and threat-model an authorization-code/PKCE-capable identity provider or an equivalent audited broker with short-lived, revocable mobile credentials; test callback ownership, token rotation, logout, CSRF/state, and cross-account isolation. Avoid introducing a new user database: WordPress remains the account authority.

## Manual staging checks
1. Unauthenticated `GET /wp-json/gmrc-pocket/v1/session` must return a REST authorization error (normally HTTP 401).
2. With an authenticated browser REST request and valid `X-WP-Nonce`, `/session` returns the current user's ID and display name; no email/password/token.
3. `/characters` still returns only the authenticated user's records. Verify using two separate test accounts and no caching between them.
4. Confirm the original Guild Gate login and logout continue to work.

## Deferred
Actual native sign-in, mobile client UI, HP writes and Guild Diceworks integration are not included in this narrow additive patch. Run the full PHPUnit suite and stage authorization tests before production deployment.
