# Phase III.M.6B — The Guild Gate Native

III.M.6B establishes a secure authentication handoff for the future Capacitor Android/iOS client without placing WordPress passwords or login cookies inside the native application.

## Authentication flow

1. The native client creates a PKCE verifier, S256 challenge, and random state.
2. `POST /wp-json/gmrc-pocket/v1/native/begin` stores only the challenge/state for ten minutes and returns a browser authorization URL.
3. The system browser opens that URL. If necessary, the existing front-end Guild Gate performs the normal WordPress login, including its existing security checks.
4. After login, GMRC issues a two-minute, single-use authorization code and redirects to `uk.co.greatmarketrealm.pocket://auth/callback` with the code and original state.
5. The app verifies state and exchanges the code + PKCE verifier at `POST /wp-json/gmrc-pocket/v1/native/token`.
6. GMRC returns a random opaque bearer token. Only its SHA-256-derived transient key is retained server-side. The token expires after 30 days.
7. Bearer authentication is accepted only for the `gmrc-pocket/v1` REST namespace. Existing browser/PWA cookie + REST nonce authentication remains unchanged.
8. `POST /wp-json/gmrc-pocket/v1/native/revoke` revokes the current native token.

Authorization codes are single-use even when the verifier is wrong. Character and REST payloads remain `private, no-store`, and offline character writes remain prohibited.

## Native client requirements

The eventual Android/iOS project must keep the access token in platform secure storage (Android Keystore-backed storage / iOS Keychain-backed storage), never Local Storage. The native client must not collect or persist the user's WordPress password.

The provisional callback scheme follows the provisional app identity: `uk.co.greatmarketrealm.pocket://auth/callback`. This can still be changed before permanent native projects/store registrations are created.

## Contract

The Pocket Native Bridge contract advances from 1.0 to **1.1** and advertises both transports:

- Browser/PWA: WordPress cookie + REST nonce.
- Native: system-browser login + PKCE + opaque bearer token.

GMRC remains authoritative; there is still no native character database.

## What this phase does not do

III.M.6B does not generate Android/iOS projects and does not yet render the character ledger in the native shell. Those clients must consume this authentication contract rather than bypassing it. The existing PWA should remain visually and functionally unchanged.
