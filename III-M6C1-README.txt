Great MarketRealm Companion — Phase III.M.6C.1 Windows Native Workshop Correction

Replace these files at the same relative paths in the plugin repository.

Changed:
- native/pocket-companion/scripts/prepare-android.mjs
- tests/Unit/Mobile/PocketFirstNativeExpeditionRegressionTest.php

Added:
- docs/Phase-III.M.6C.1-Windows-Native-Workshop-Correction.md

After replacing the files locally in native/pocket-companion, run:
  npm run android:prepare

The command should now install/retain the GMRC callback intent filter and complete `cap sync android` without spawning npx.cmd.
