Phase III.M.6B — The Guild Gate Native

Apply this patch over the completed III.M.6A repository.

Key changes:
- Secure system-browser Guild Gate handoff for native clients.
- PKCE S256 challenge/verifier flow with state round-trip.
- Single-use, two-minute authorization codes.
- Opaque 30-day Pocket bearer tokens, stored server-side only by hashed transient key.
- Bearer authentication restricted to gmrc-pocket/v1 routes.
- Native token revocation endpoint.
- Existing browser/PWA cookie + REST nonce flow preserved.
- Native Bridge contract advanced to 1.1.
- No WordPress password handling in the native shell.
- No character caching or offline character writes.

Starting confirmed baseline: 3966 tests / 14822 assertions.
This patch adds one regression test and updates the III.M.6A contract expectation.
