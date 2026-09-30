# Phase III.M.7A — The Release Forge

The Pocket release identity is `uk.co.greatmarketrealm.pocket`. Source-controlled release metadata lives in `native/pocket-companion/config/release.json`; the first Play test release is `1.0.0` with `versionCode` 1. Increment `versionCode` for every bundle uploaded to Google Play.

## Signing boundary

Use a dedicated **upload key** for Google Play. The keystore and all passwords are local secrets and must never be committed. Google Play App Signing should manage the app-signing key; the local upload key signs bundles submitted to Play.

Create the upload key once with Android Studio (Build > Generate Signed Bundle / APK > Create new) or `keytool`, store it outside the repository, and keep a secure backup. Set these environment variables in the terminal used for the release build: `GMRC_UPLOAD_STORE_FILE`, `GMRC_UPLOAD_STORE_PASSWORD`, `GMRC_UPLOAD_KEY_ALIAS`, `GMRC_UPLOAD_KEY_PASSWORD`.

## Build

From `native/pocket-companion` run `npm run build`, then `npm run android:prepare`, then `npm run android:bundle`. The signed AAB is emitted under `android/app/build/outputs/bundle/release/`. Do not commit the generated Android project or the AAB.

Before uploading, verify the application ID, version name/code, launcher artwork, Guild Gate deep link, and that a release build can authenticate and restore its secure Guild key on a physical Pixel.
