Phase III.M.5A — The Installable Pocket Companion

Files in this patch:
- app/Mobile/PocketPage.php
- app/Mobile/PocketAppFoundation.php
- assets/images/pocket/app-icon-192.png
- assets/images/pocket/app-icon-512.png
- tests/Unit/Mobile/PocketAppFoundationRegressionTest.php

Purpose:
Adds a web app manifest, standalone display metadata, home-screen icons and a deliberately conservative service worker. The service worker caches only public Pocket artwork; authenticated pages, REST character data and gameplay mutations remain network-only.

After upload, clear site/browser caches and run:
php vendor/bin/phpunit --display-warnings

Then visit the published Pocket Companion page over HTTPS and test installation from Android/Chrome and iOS/Safari Add to Home Screen.
