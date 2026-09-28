# Phase III.M.6C — The First Native Expedition

III.M.6C turns the Native Workshop into the first runnable Android client. Its sole job is to prove the complete native authentication boundary before character UI is introduced.

## Expedition flow

1. The local Capacitor shell creates a PKCE verifier, S256 challenge and random state.
2. `native/begin` returns the existing Guild Gate authorization URL.
3. `@capacitor/browser` opens that URL in the Android system browser.
4. The existing WordPress Guild Gate authenticates the member.
5. Android routes `uk.co.greatmarketrealm.pocket://auth/callback` back into the app.
6. `@capacitor/app` receives the callback; the client verifies state and exchanges the single-use code.
7. The returned bearer token is immediately checked against `/gmrc-pocket/v1/session` and the native shell displays the authenticated Guild member name.
8. Leave the Guild revokes the token server-side.

## Security boundary

The WordPress password is never collected by the native shell. III.M.6C deliberately keeps the access token **in process memory only**. It is not written to Local Storage, Preferences, a file, or the generated Android project. Closing the process therefore requires a fresh Guild Gate sign-in. Persistent native sessions remain blocked until a reviewed Android Keystore / iOS Keychain-backed storage implementation is added.

No character payload is requested or cached in this phase. GMRC remains authoritative.

## Android preparation

From `native/pocket-companion`, with Node.js 22+ and Android Studio installed:

```
npm install
npm run build
npm run android:prepare
npm run cap:open:android
```

`android:prepare` generates the Capacitor Android project if necessary, adds the narrowly-scoped callback intent filter, and synchronises the web bundle/plugins. The generated `android/` project should be reviewed and committed only after it builds successfully on the development machine.

## Acceptance test

The Pixel build must launch the local Pocket shell, open the real Guild Gate in the system browser, return through the registered callback, verify `/session`, display the authenticated member name, revoke successfully, and contain no character data.
