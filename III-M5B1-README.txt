Great MarketRealm Companion
Phase III.M.5B.1 — The Guild Gate Goes Dark

Purpose
-------
Correct the installed Pocket Companion cold-offline launch discovered during Pixel field testing.

Changes
-------
- Adds a dedicated generic offline virtual asset.
- Pre-caches only that public offline guard page plus existing public Pocket artwork.
- Navigation remains network-first and private character pages/API responses are never cached.
- Cold offline navigation falls back to the pre-cached MarketRealm offline page.
- Bumps the Pocket static cache to v2 so an installed M.5B service worker replaces the previous cache.
- Adds regression coverage for the cold-launch path.

Expected field behaviour
------------------------
1. Open Pocket online once after deployment so the updated service worker can install.
2. Fully close the installed Pocket app.
3. Disable Wi-Fi and mobile data.
4. Launch Pocket from its installed app icon.
5. After the normal Android splash, the MarketRealm offline card should appear with
   “The Guild is out of reach.” and a “Try again” button.
6. Restore connectivity and tap Try again; Pocket should return to the live Guild Gate.

Safety boundary
---------------
No character records, REST responses, credentials, HP, spell slots, or authenticated pages are added to Cache Storage.
