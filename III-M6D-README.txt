Phase III.M.6D — The Keeper Remembers Your Key

Overlay this patch at the root of great-marketrealm-companion.

Server:
  php vendor/bin/phpunit --display-warnings

Windows native workstation (important: this phase adds one npm dependency):
  cd native\pocket-companion
  npm install
  npm run build
  npm run android:prepare

Then press Run in Android Studio with the Pixel selected.

Field test:
1. Sign in once through the Guild Gate.
2. Confirm happy thumbs-up Auby + “The Keeper remembers your key.”
3. Fully close the Pocket Companion app (do not press Leave the Guild).
4. Relaunch from the Android launcher.
5. Confirm Pocket restores the session without opening the browser.
6. Press Leave the Guild.
7. Fully close/reopen and confirm Pocket stays signed out.
8. Enter the Guild again and confirm normal PKCE browser authentication still works.

Do not use npm audit fix during this phase; keep dependency changes deliberate.
