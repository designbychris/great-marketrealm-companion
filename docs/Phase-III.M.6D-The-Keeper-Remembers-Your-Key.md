# Phase III.M.6D — The Keeper Remembers Your Key

The Pocket Companion now treats the browser Guild Gate as onboarding/re-authentication rather than a normal launch step.

## Native session lifecycle

1. The existing PKCE + system-browser Guild Gate remains the only credential-entry path.
2. After token exchange, Pocket verifies the token against `/gmrc-pocket/v1/session`.
3. A verified bearer token is stored with `@aparajita/capacitor-secure-storage`.
4. Android stores encrypted data using a key held by Android Keystore; iOS uses the app Keychain.
5. On a later launch, Pocket reads the token from secure storage and revalidates `/session`.
6. A valid token enters the authenticated state without opening the browser.
7. An invalid/revoked token is deleted and the Guild Gate is shown.
8. Leave the Guild revokes the server token and removes the local secure copy.

The JavaScript explicitly refuses secure-token persistence outside a native Capacitor platform, preventing the secure-storage plugin's web fallback from becoming an authentication-token store.

No WordPress password is stored by Pocket. No character data is persisted by this phase.

## Auby states

- Gate/checking: canonical welcoming Auby.
- Authenticated: supplied happy thumbs-up Auby and “The Keeper remembers your key.”
