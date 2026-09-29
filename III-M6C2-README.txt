Phase III.M.6C.2 — Auby Takes the Gate

Overlay this patch at the root of great-marketrealm-companion.

After deployment:
  php vendor/bin/phpunit --display-warnings

On the Windows native workstation:
  npm run build
  npm run android:prepare

Then rebuild/run from Android Studio. The generated Android launcher resources are reapplied by android:prepare, so the android/ directory does not become the canonical source.

This patch preserves the supplied Auby PNG unchanged as the Pocket branding master and replaces the standalone eggplant product mark with Auby.
