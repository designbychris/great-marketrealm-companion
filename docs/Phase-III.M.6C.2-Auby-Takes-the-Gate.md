# Phase III.M.6C.2 — Auby Takes the Gate

Auby becomes the canonical user-facing mascot for the Pocket Companion.

## Changes
- Retires the standalone eggplant emoji from the native Guild Gate.
- Preserves the supplied Auby artwork as `assets/images/pocket/auby-pocket-master.png`.
- Uses Auby in the native Guild Gate medallion.
- Rebuilds the 192px and 512px Pocket/PWA icons around Auby.
- Adds reproducible Android launcher resources for legacy, round and adaptive launchers.
- Extends `android:prepare` so generated Android projects receive the source-controlled Auby launcher artwork after every Capacitor sync.
- Keeps the III.M.6C security model unchanged: PKCE, system-browser Guild Gate, memory-only bearer token, and no native character database.

The generated `android/` directory remains disposable. Canonical branding lives in source-controlled Pocket assets and is reapplied by `npm run android:prepare`.
